#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <SPI.h>
#include <MFRC522.h>
#include <ESP32Servo.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <time.h>

#define RST_PIN 22
#define SS_PIN 21

MFRC522 mfrc522(SS_PIN, RST_PIN);

byte readCard[4];
String tagID = "";

int check = 0;

#define g_led 25 // Green LED Pin
#define y_led 26 // Yellow LED Pin
#define r_led 27 // Red LED Pin

#define buzzer 14 // Buzzer Pin
#define ir_sensor 12 // IR Sensor Pin

Servo myservo; // Create a servo object
LiquidCrystal_I2C lcd(0x27, 16, 2); // Set the LCD address to 0x27 for a 16 chars and 2 line display

// Replace with your WiFi credentials
const char* ssid = "kir";
const char* password = "password";

// NTP Server
const char* ntpServer = "pool.ntp.org";
const long gmtOffset_sec = 8 * 3600; // GMT offset for Malaysia (UTC+8)
const int daylightOffset_sec = 0;

// Server URL
String serverName = "http://172.20.10.3/autogate/monitor_data.php"; // Replace with your local IP and script path

void setup() {
  Serial.begin(9600); // Initiating
  SPI.begin(); // SPI bus
  mfrc522.PCD_Init(); // MFRC522

  // Initialize I2C with custom pins
  Wire.begin(33, 32); // SDA = GPIO 33, SCL = GPIO 32
  lcd.init(); // Initialize the LCD
  lcd.backlight(); // Turn on the backlight
  lcd.setCursor(0, 0);
  lcd.print("Welcome"); // Display Welcome on the LCD

  pinMode(g_led, OUTPUT);
  pinMode(y_led, OUTPUT);
  pinMode(r_led, OUTPUT);

  myservo.attach(13); // Attach the servo on pin 13

  pinMode(buzzer, OUTPUT);
  pinMode(ir_sensor, INPUT);

  digitalWrite(g_led, 0);
  digitalWrite(y_led, 0);
  digitalWrite(r_led, 1);

  delay(500);

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");

  configTime(gmtOffset_sec, daylightOffset_sec, ntpServer);
}

void loop() {
  // Wait until new tag is available
  if (getID()) {
    Serial.println(tagID);
    check = 0;
    // Sound the buzzer once when a tag is detected
    digitalWrite(buzzer, 1);
    delay(100); // Buzzer on for 100 milliseconds
    digitalWrite(buzzer, 0);

    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;
      String url = serverName + "?cardid=" + tagID;
      http.begin(url);

      int httpResponseCode = http.GET();

      if (httpResponseCode > 0) {
        String response = http.getString();
        Serial.println(httpResponseCode);
        Serial.println(response);

        DynamicJsonDocument doc(1024);
        deserializeJson(doc, response);
        String residentname = doc["residentname"].as<String>();

        // If residentname is "Unknown", display Access Denied and do not open the gate
        if (residentname != "Unknown") {
          String platenumber = doc["platenumber"].as<String>();
          String address = doc["address"].as<String>();

          Serial.print("RFID Tag: ");
          Serial.print(tagID);
          Serial.print(", Resident Name: ");
          Serial.print(residentname);
          Serial.print(", Plate Number: ");
          Serial.print(platenumber);
          Serial.print(", Address: ");
          Serial.println(address);

          lcd.clear();
          lcd.setCursor(0, 0);
          lcd.print("Access Granted"); // Display Access Granted
          digitalWrite(g_led, 1);
          digitalWrite(y_led, 0);
          digitalWrite(r_led, 0);
          myservo.write(0); // Move servo to 90 degrees anti-clockwise

          // Check IR sensor status
          unsigned long startTime = millis();
          while (millis() - startTime < 10000) {
            if (digitalRead(ir_sensor) == LOW) { // IR sensor detects the vehicle
              delay(500); // Small delay to ensure the vehicle has passed
              while (digitalRead(ir_sensor) == LOW); // Wait until vehicle is no longer detected
              break; // Exit the loop when the vehicle passes
            }
          }

          delay(100);
          digitalWrite(g_led, 0);
          digitalWrite(y_led, 1);
          digitalWrite(r_led, 0);
          delay(1000);
          myservo.write(90); // Close the gate after vehicle passes or after 10 seconds

          // Get current date and time
          struct tm timeinfo;
          if (!getLocalTime(&timeinfo)) {
            Serial.println("Failed to obtain time");
            return;
          }

          char currentDate[11];
          char currentTime[9];
          strftime(currentDate, 11, "%Y-%m-%d", &timeinfo);
          strftime(currentTime, 9, "%H:%M:%S", &timeinfo);

          // Send data to PHP script to store in MySQL
          http.begin(serverName);
          http.addHeader("Content-Type", "application/x-www-form-urlencoded");
          String httpRequestData = "cardid=" + tagID + "&residentname=" + residentname + "&platenumber=" + platenumber + "&address=" + address + "&date=" + currentDate + "&time=" + currentTime + "&status=IN";
          int httpResponseCodePost = http.POST(httpRequestData);

          if (httpResponseCodePost > 0) {
            String postResponse = http.getString();
            Serial.println(httpResponseCodePost);
            Serial.println(postResponse);
          } else {
            Serial.print("Error on sending POST: ");
            Serial.println(httpResponseCodePost);
          }

          http.end();
        } else {
          lcd.clear();
          lcd.setCursor(0, 0);
          lcd.print("Access Denied"); // Display Access Denied
          for (int x = 0; x < 3; x++) {
            digitalWrite(g_led, 0);
            digitalWrite(y_led, 0);
            digitalWrite(r_led, 0);
            digitalWrite(buzzer, 1); // Turn on the buzzer
            delay(500);
            digitalWrite(y_led, 1);
            digitalWrite(buzzer, 0); // Turn off the buzzer
            delay(500);
          }
        }
      } else {
        Serial.print("Error on sending GET: ");
        Serial.println(httpResponseCode);
      }

      http.end();
    } else {
      Serial.println("WiFi Disconnected");
    }

    digitalWrite(g_led, 0);
    digitalWrite(y_led, 0);
    digitalWrite(r_led, 1);
  } else {
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Welcome"); // Display Welcome when no RFID is detected
  }
  delay(10);
}

// Read new tag if available
boolean getID() {
  // Getting ready for Reading PICCs
  if (!mfrc522.PICC_IsNewCardPresent()) {
    // If a new PICC placed to RFID reader continue
    return false;
  }
  if (!mfrc522.PICC_ReadCardSerial()) {
    // Since a PICC placed get Serial and continue
    return false;
  }
  tagID = "";
  for (uint8_t i = 0; i < 4; i++) {
    // The MIFARE PICCs that we use have 4 byte UID
    tagID.concat(String(mfrc522.uid.uidByte[i], HEX)); // Adds the 4 bytes in a single String variable
  }
  tagID.toUpperCase();
  mfrc522.PICC_HaltA(); // Stop reading
  return true;
}

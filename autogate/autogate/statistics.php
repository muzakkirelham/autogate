<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .navbar {
            font-family: Arial, sans-serif;
            overflow: hidden;
            background-color: #333;
            padding: 10px 20px;
        }

        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }

        .navbar a.active {
            background-color: #04AA6D;
            color: white;
        }

        .chart-container {
            width: 80%;
            margin: 20px auto;
            text-align: center;
        }

        .filter-container {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Styling the date picker */
        #dateFilter {
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: auto;
            text-align: center;
        }

        #dateFilter:focus {
            border-color: #04AA6D;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 128, 0, 0.3);
        }

        .filter-container label {
            font-size: 18px;
            margin-right: 8px;
            color: #333;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="admin.php">Admin</a>
    <a href="view.php">View</a>
    <a href="monitor.php">Monitor</a>
    <a href="statistics.php">Statistics</a>
    <a href="payment.php">Payment</a>
</div>

<h1 align="center">Residents Statistics</h1>

<div class="filter-container">
    <label for="dateFilter">Select Date for IN/OUT Statistics:</label>
    <input type="date" id="dateFilter" name="dateFilter" value="<?php echo date('Y-m-d'); ?>">
</div>

<div class="chart-container">
    <div id="statusChart" style="width: 100%; height: 400px;"></div>
    <div id="raceChart" style="width: 100%; height: 400px;"></div>
    <div id="paymentChart" style="width: 100%; height: 400px;"></div>
</div>

<!-- Load the Highcharts library -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<script>
    function loadChartData(selectedDate) {
        // Fetch data from the PHP script with the selected date
        fetch(`get_statistics.php?scan_date=${selectedDate}`)
            .then(response => response.json())
            .then(data => {
                // "IN/OUT" Status Pie Chart
                Highcharts.chart('statusChart', {
                    chart: {
                        type: 'pie'
                    },
                    title: {
                        text: `IN/OUT Status on ${selectedDate}`
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                    },
                    accessibility: {
                        point: {
                            valueSuffix: '%'
                        }
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f}%)'
                            }
                        }
                    },
                    series: [{
                        name: 'Status',
                        colorByPoint: true,
                        data: data.statusData // Dynamically populated data
                    }]
                });

                // Race Pie Chart
                Highcharts.chart('raceChart', {
                    chart: {
                        type: 'pie'
                    },
                    title: {
                        text: 'Race Distribution'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                    },
                    accessibility: {
                        point: {
                            valueSuffix: '%'
                        }
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f}%)'
                            }
                        }
                    },
                    series: [{
                        name: 'Race',
                        colorByPoint: true,
                        data: data.raceData // Dynamically populated data
                    }]
                });

                // Payment Month Horizontal Bar Chart
                Highcharts.chart('paymentChart', {
                    chart: {
                        type: 'bar',
                        inverted: true
                    },
                    title: {
                        text: 'Payment Statistics by Month'
                    },
                    xAxis: {
                        type: 'category',
                        title: {
                            text: 'Month'
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Number of Payments'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    tooltip: {
                        pointFormat: '<b>{point.y}</b> payments'
                    },
                    plotOptions: {
                        series: {
                            dataLabels: {
                                enabled: true,
                                format: '{point.y}'
                            }
                        }
                    },
                    series: [{
                        name: 'Payments',
                        colorByPoint: true,
                        data: data.paymentData // Dynamically populated data
                    }]
                });
            });
    }

    // Load chart data for the initially selected date
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('dateFilter');
        loadChartData(dateInput.value);

        // Update chart data when the date is changed
        dateInput.addEventListener('change', function() {
            loadChartData(this.value);
        });
    });
</script>

</body>
</html>

$(function () {
    var $emChartBox = $('#em-chart');
    var chartData = defs['em-chart-data'];

    if (PsUtil.hasGlobalObject('google') && PsIs.array(chartData)) {
        $emChartBox.show();

        function drawChart() {

            chartData.unshift(['Эмоция', 'Количество сообщений']);
            var data = google.visualization.arrayToDataTable(chartData);

            var formatter = new google.visualization.NumberFormat({pattern: '###.###'});
            formatter.format(data, 1);

            var options = {
                title: 'Распределение эмоций',
                is3D: true,
                colors: ['#fa0', '#3366CC', '#DC3912', '#990099', '#109618', '#444'],
                legend: {
                    position: 'left',
                    alignment: 'top'
                },
                chartArea: {
                    backgroundColor: 'transparent'
                }
            };

            $emChartBox.empty().show();

            var chart = new google.visualization.PieChart($emChartBox[0]);

            chart.draw(data, options);
        }

        google.charts.load('current', {'packages': ['corechart']});
        google.charts.setOnLoadCallback(drawChart);

    } else {
        $emChartBox.remove();
    }

});
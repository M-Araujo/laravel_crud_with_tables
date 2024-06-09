<div class="max-w-sm w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
    <div class="py-6" id="donut-chart"></div>
</div>

<script>

    /* Pie chart is used to specific data / boolean data only */

    //  console.log({!! json_encode($data) !!});

    let labels = [];
    let chartValues = [];

    function formatLabelsForEasyReading() {
        let keys = Object.keys({!! json_encode($data) !!});

        for (const [key, value] of Object.entries({!! json_encode($data) !!})) {
            if (key === '0') {
                labels.push('No');
                chartValues.push(Object.keys(value).length);
            } else {
                labels.push('yes');
                chartValues.push(Object.keys(value).length);
            }
        }
    }

    const getChartOptions = () => {
        return {
            series: chartValues,
            colors: ["#1C64F2", "#16BDCA", "#FDBA8C", "#E74694"],
            chart: {
                height: 320,
                width: "100%",
                type: "donut",
            },
            stroke: {
                colors: ["transparent"],
                lineCap: "",
            },
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontFamily: "Inter, sans-serif",
                                offsetY: 20,
                            },
                            total: {
                                showAlways: true,
                                show: true,
                                label: "Total of users",
                                fontFamily: "Inter, sans-serif",
                                formatter: function (w) {
                                    const sum = w.globals.seriesTotals.reduce((a, b) => {
                                        return a + b
                                    }, 0)
                                    return sum
                                },
                            },
                            value: {
                                show: true,
                                fontFamily: "Inter, sans-serif",
                                offsetY: -20,
                                formatter: function (value) {
                                    return value;
                                },
                            },
                        },
                        size: "80%",
                    },
                },
            },
            grid: {
                padding: {
                    top: -2,
                },
            },
            labels: labels,
            dataLabels: {
                enabled: false,
            },
            legend: {
                position: "bottom",
                fontFamily: "Inter, sans-serif",
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return value + ""
                    },
                },
            },
            xaxis: {
                labels: {
                    formatter: function (value) {
                        return value + ""
                    },
                },
                axisTicks: {
                    show: false,
                },
                axisBorder: {
                    show: false,
                },
            },
        }
    }

    if (document.getElementById("donut-chart") && typeof ApexCharts !== 'undefined') {
        formatLabelsForEasyReading();
        const chart = new ApexCharts(document.getElementById("donut-chart"), getChartOptions());
        chart.render();
    }

</script>
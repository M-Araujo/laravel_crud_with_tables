<div class="max-w-sm w-full bg-white rounded-lg shadow dark:bg-gray-800 p-4 md:p-6">
    <div class="py-6" id={{$id}}></div>
</div>


<script>
   

    const getChartOptions_{{$id}} = () => {
        return {
            series: {{$data->pluck('count')->toJson()}},
            colors: ['#05505c', '#036672', '#047481', '#0694a2', '#16bdca', '#7edce2', '#afecef', '#d5f5f6', '#edfafa'],
            chart: {
                height: 420,
                width: "100%",
                type: "pie",
            },
            stroke: {
                colors: ["white"],
                lineCap: "",
            },
            plotOptions: {
                pie: {
                    labels: {
                        show: true,
                    },
                    size: "100%",
                    dataLabels: {
                        offset: -25
                    }
                },
            },
            labels: {!! $data->pluck('name')->toJson()!!}
            ,
            dataLabels: {
                enabled: true,
                style: {
                    fontFamily: "Inter, sans-serif",
                },
            },
            legend: {
                position: "bottom",
                fontFamily: "Inter, sans-serif",
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return value
                    },
                },
            },
            xaxis: {
                labels: {
                    formatter: function (value) {
                        return value
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


    if (document.getElementById({{$id}}) && typeof ApexCharts !== 'undefined') {
        let chart_{{$id}} = new ApexCharts(document.getElementById({{$id}}), getChartOptions_{{$id}}());
        chart_{{$id}}.render();
    }
</script>
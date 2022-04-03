import * as echarts from 'echarts';

document.addEventListener('DOMContentLoaded', function() {
    let getValue = document.querySelector('.data-graphics');
    let success = getValue.dataset.success;
    let failed = getValue.dataset.failed;

    // Charge le graphique avec les données adéquates
    showGraphics(success, failed);
});


/*
    Permet d'afficher les graphiques en fonction des données
    des matières validés de l'étudiant.

    ectsSuccess = string
    ectsFailed = string
 */
function showGraphics(etcsSuccess, etcsFailed){

    var chartDom = document.getElementById('etcsSuccess');
    var myChart = echarts.init(chartDom);
    var option;
    var colorPalette = ['#0ca94f', '#D2433F'];

    option = {
        series: [
            {
                name: 'ETCS 2022',
                type: 'pie',
                radius: ['40%', '70%'],
                label: {
                    show: true,
                    position: 'center',
                    color:'#053869',
                    formatter:etcsSuccess + '%',
                    textStyle: {
                        fontSize: '30',
                        fontWeight: 'bold'
                    },

                },
                color: colorPalette,
                labelLine: {
                    show: false
                },
                data: [
                    { value: etcsSuccess, name: 'ETCS Validés'},
                    { value: etcsFailed, name: 'ECTS Non validés' }
                ],

            }
        ]
    };

    option && myChart.setOption(option);

}
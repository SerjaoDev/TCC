document.addEventListener("DOMContentLoaded", function(){
    const elementoGrafico = document.getElementById("grafico");

    if(!elementoGrafico){
        return;
    }

    const nomes = typeof nomesTurmas !== "undefined" ? nomesTurmas : [];
    const medias = typeof mediasTurmas !== "undefined" ? mediasTurmas : [];

    new Chart(elementoGrafico,{
        type:"line",
        data:{
            labels:nomes,
            datasets:[{
                label:"Média de acertos (%)",
                data:medias,
                borderWidth:3,
                tension:0.4,
                fill:false
            }]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false,

            plugins:{
                legend:{
                    display:true
                }
            },

            scales:{
                y:{
                    beginAtZero:true,
                    max:100
                }
            }
        }
    });
});
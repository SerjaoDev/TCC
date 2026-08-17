document.addEventListener("DOMContentLoaded", () => {
    const elementoGrafico =
        document.getElementById("grafico");

    if (!elementoGrafico) {
        return;
    }

    if (
        typeof Chart === "undefined"
    ) {
        console.error(
            "Chart.js não foi carregado."
        );

        return;
    }

    const nomes =
        Array.isArray(window.nomesTurmas)
            ? window.nomesTurmas
            : [];

    const medias =
        Array.isArray(window.mediasTurmas)
            ? window.mediasTurmas
            : [];

    new Chart(
        elementoGrafico,
        {
            type: "line",
            data: {
                labels: nomes,
                datasets: [
                    {
                        label:
                            "Média de acertos (%)",
                        data: medias,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: false,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: "index"
                },
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 0,
                        max: 100,
                        ticks: {
                            callback: (valor) => {
                                return valor + "%";
                            }
                        }
                    }
                }
            }
        }
    );
});
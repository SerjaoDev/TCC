document.addEventListener("DOMContentLoaded", () => {
    console.log("Sistema Lumi carregado.");

    const formulariosExcluir =
        document.querySelectorAll(
            'form[data-confirmar-exclusao]'
        );

    formulariosExcluir.forEach((formulario) => {
        formulario.addEventListener(
            "submit",
            (event) => {
                const mensagem =
                    formulario.dataset.confirmarExclusao ||
                    "Tem certeza que deseja excluir este item?";

                const confirmar =
                    window.confirm(mensagem);

                if (!confirmar) {
                    event.preventDefault();
                }
            }
        );
    });
});
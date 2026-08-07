document.addEventListener("DOMContentLoaded", function(){
    console.log("Sistema Lumi carregado.");

    const botoes = document.querySelectorAll("button");

    botoes.forEach(function(botao){
        botao.addEventListener("click",function(){
            if(botao.classList.contains("excluir")){
                const confirmar = confirm("Tem certeza que deseja excluir?");

                if(!confirmar){
                    event.preventDefault();
                }
            }
        });
    });
});
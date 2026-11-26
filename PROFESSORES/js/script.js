console.log('Sistema Lumi carregado.');

// Mostrar / ocultar senha (funciona em um ou vários campos)
document.querySelectorAll('.toggle-senha').forEach((botao) => {
    const campoSenha = botao.parentElement.querySelector('input');

    if (!campoSenha) return;

    botao.addEventListener('click', () => {
        const mostrar = campoSenha.type === 'password';

        campoSenha.type = mostrar ? 'text' : 'password';
        botao.classList.toggle('is-visible', mostrar);
        botao.setAttribute('aria-label', mostrar ? 'Ocultar senha' : 'Mostrar senha');
        botao.setAttribute('aria-pressed', String(mostrar));
        campoSenha.focus();
    });
});

// Brilho do cartão acompanha o cursor
const cartao = document.querySelector('.login-card');
const reduzMovimento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (cartao && !reduzMovimento) {
    cartao.addEventListener('pointermove', (evento) => {
        const retangulo = cartao.getBoundingClientRect();
        cartao.style.setProperty('--mx', `${evento.clientX - retangulo.left}px`);
        cartao.style.setProperty('--my', `${evento.clientY - retangulo.top}px`);
    });

    cartao.addEventListener('pointerleave', () => {
        cartao.style.removeProperty('--mx');
        cartao.style.removeProperty('--my');
    });
}

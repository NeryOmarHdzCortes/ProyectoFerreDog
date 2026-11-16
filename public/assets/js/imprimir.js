document.addEventListener('DOMContentLoaded', function () {
    const botonImprimir = document.getElementById('imprimirTicket');
    if (!botonImprimir) {
        return;
    }

    botonImprimir.addEventListener('click', function () {
        window.print();
    });
});


    document.addEventListener("DOMContentLoaded", function () {
    // Functia de a schimba modalele
    function switchModal(hideModalId, showModalId) {
        var hideModal = new bootstrap.Modal(document.getElementById(hideModalId));
        var showModal = new bootstrap.Modal(document.getElementById(showModalId));

        // Ascunde modalul curent
        hideModal.hide();

        // Arată modalul nou
        showModal.show();
    }

    // Evenimente pentru link-urile care schimbă modalurile
    document.querySelectorAll('[data-switch-modal]').forEach(function (el) {
    el.addEventListener("click", function (event) {
    event.preventDefault();

    // Preia ID-urile modalurilor de la atributele data-hide și data-show
    let hideModal = el.getAttribute("data-hide");
    let showModal = el.getAttribute("data-show");

    // Apelează funcția pentru a schimba modalele
    switchModal(hideModal, showModal);
        });
    });
});



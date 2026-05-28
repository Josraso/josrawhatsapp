/* JosraWhatsapp - JS frontend */
document.addEventListener('DOMContentLoaded', function () {
    var link = document.getElementById('josra_whatsapp_link');
    if (!link) return;

    var mobileHref = link.getAttribute('data-mobile-href');
    if (!mobileHref) return;

    var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    if (isMobile) {
        link.setAttribute('href', mobileHref);
    }
});

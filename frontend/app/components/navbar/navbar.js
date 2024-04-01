import config from "../../utils/config.js";
import divIdMapping from "../../utils/divIdMapping.js";
import mainHandler from '../mainHandler.js';

export function initNavbar() {
    $('#' + divIdMapping.components.navbar).load(config.links.host + 'app/components/navbar/navbar.html', function() {
        buildAllLinksHrefNavbar();

        switch (true) {
            case mainHandler.isDashboardPageVisible():
                buildNavbarConfiguration(divIdMapping.pages.navbar.dashboard.id, divIdMapping.pages.navbar.dashboard.href);
                break;
            case mainHandler.isFiltroPageVisible():
                buildNavbarConfiguration(divIdMapping.pages.navbar.filtro.id, divIdMapping.pages.navbar.filtro.href);
                break;
            case mainHandler.isAdicionarClientePageVisible():
                buildNavbarConfiguration(divIdMapping.pages.navbar.cliente.id, divIdMapping.pages.navbar.cliente.href);
                break;
            case mainHandler.isAdicionarProcessoPageVisible():
                buildNavbarConfiguration(divIdMapping.pages.navbar.processo.id, divIdMapping.pages.navbar.processo.href);
                break;
        }
    });
}

function buildNavbarConfiguration(idElement) {
    $("#" + idElement).addClass("active");
    $("#" + idElement + " a ").attr('aria-current', 'page');
}

function buildAllLinksHrefNavbar() {
    buildHrefLinksNavbar(divIdMapping.pages.navbar.dashboard.id, divIdMapping.pages.navbar.dashboard.href);
    buildHrefLinksNavbar(divIdMapping.pages.navbar.filtro.id, divIdMapping.pages.navbar.filtro.href);
    buildHrefLinksNavbar(divIdMapping.pages.navbar.cliente.id, divIdMapping.pages.navbar.cliente.href);
    buildHrefLinksNavbar(divIdMapping.pages.navbar.processo.id, divIdMapping.pages.navbar.processo.href);
}

function buildHrefLinksNavbar(idElement, href){
    $("#" + idElement + " a ").attr('href', config.links.host + href);

}
import config from "../../utils/config.js";
import divIdMapping from "../../utils/divIdMapping.js";

export function initHeader() {
    $('#' + divIdMapping.components.header).load(config.links.host + 'app/components/header/header.html', function() {
        // Selecionar todos os links de stylesheet dentro do header
        carregarLinkStyleCss();
    });
}

function carregarLinkStyleCss() {
    $('head link[rel="stylesheet"]').each(function() {
        // Obter o caminho atual do link
        let currentHref = $(this).attr('href');
        // Se o link começar com o caminho atual, altere-o
        if (currentHref.startsWith('/app/')) {
            $(this).attr('href', config.links.host + currentHref);
        }
    });
}
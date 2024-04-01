import { initHeader } from './components/header/header.js';
import { initNavbar } from './components/navbar/navbar.js';
import { initFooter } from './components/footer/footer.js';

initHeader();
initNavbar();
initFooter();

$(document).ready(function() {
    // $('[data-toggle="tooltip"]').tooltip();
    // switch (true) {
    //     case paginaAtiva.isListaClientesWebservice():
    //         autoComplete.initializeAutocomplete('clienteContainer', 'clienteFiltro', 'listaClientes', 'clienteFiltroFilter', 'Carregando...');
    //         break;
    //     case paginaAtiva.isCadastrarClienteWebservice():
    //         autoComplete.initializeAutocomplete('clienteContainer', 'clienteFiltro', 'listaClientes', 'clienteFiltroFilter', 'Carregando...', main.capturarDadosClientes);
    //         break;
    // }

});
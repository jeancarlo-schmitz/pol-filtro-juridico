import divIdMapping from "../utils/divIdMapping.js";

const mainHandler = {};

// mainHandler.goToStep1 = function () {
//     window.location = './passo1.php';
// }

mainHandler.isDashboardPageVisible = function () {
    return $("#" + divIdMapping.pages.dashboard.id).is(':visible');
};

mainHandler.isFiltroPageVisible = function () {
    return $("#" + divIdMapping.pages.filtro.id).is(':visible');
};

mainHandler.isAdicionarClientePageVisible = function () {
    return $("#" + divIdMapping.pages.filtro.id).is(':visible');
};

mainHandler.isAdicionarProcessoPageVisible = function () {
    return $("#" + divIdMapping.pages.filtro.id).is(':visible');
};

export default mainHandler;
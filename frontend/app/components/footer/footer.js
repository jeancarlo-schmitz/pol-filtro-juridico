import config from "../../utils/config.js";
import divIdMapping from "../../utils/divIdMapping.js";

export function initFooter() {
    $('#' + divIdMapping.components.footer).load(config.links.host + 'app/components/header/header.html');
}
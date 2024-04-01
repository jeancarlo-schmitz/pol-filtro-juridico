const config = {

};

if (['localhost', '127.0.0.1', '10.0.12.201'].some(h => location.hostname.startsWith(h))) {
    config.amb = 'dev';
}

config.isDev = function(){
    return config.amb === 'dev';
};

let host = getFullHostUrl();

config.links = {
    urlIntranet: config.isDev() ? host + '/atitude_juridica_sistema/index.php?' : 'https://intranet.atitudejuridica.com.br/index.php?',
    urlDownloadIntranet: config.isDev() ? host + '/atitude_juridica_sistema/' : 'https://intranet.atitudejuridica.com.br/',
    host: config.isDev() ? host + '/pol-filtro-juridico/frontend/' : host
};

export default config;


function getFullHostUrl() {
    var protocol = window.location.protocol;
    var host = window.location.hostname;
    var port = window.location.port;

    if (port && port !== "80" && port !== "443") {
        host += ":" + port;
    }

    return protocol + "//" + host;
}
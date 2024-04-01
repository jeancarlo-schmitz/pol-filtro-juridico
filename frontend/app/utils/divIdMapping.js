const divIdMapping = {
    pages: {
        dashboard: {
            id: "dashboard-page",

        },
        filtro:{
            id: "filtro-page"
        },
        cliente: {
            id: "criar-cliente-page"
        },
        processo: {
            id: "criar-processo-page"
        },
        navbar: {
            dashboard:{
                id: "navbar-dashboard",
                href: "/app/modules/dashboard/view/dashboard.html"
            },
            filtro:{
                id: "navbar-filtro",
                href: "/app/modules/filtro/view/filtro.html"
            },
            processo:{
                id: "navbar-processo",
                href: "/app/modules/criarProcesso/view/criarProcesso.html"
            },
            cliente:{
                id:  "navbar-cliente",
                href: "/app/modules/criarCliente/view/criarCliente.html"
            },
        }
    },
    components: {
        header: "header",
        navbar: "navbar",
        footer: "footer"
    }
};


export default divIdMapping;
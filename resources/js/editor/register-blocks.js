const { registerBlockType } = wp.blocks;
const { serverSideRender: ServerSideRender } = wp;

registerBlockType("owc/gravityforms-zaaksysteem", {
	title: "Zaken",
	category: "theme",
	edit: () => {
		return <ServerSideRender block="owc/gravityforms-zaaksysteem" />;
	},
	save: () => () => null,
});

registerBlockType("owc/mijn-zaken", {
    title: "Mijn Zaken",
    category: "common",
    edit: () => {
        return <ServerSideRender block="owc/mijn-zaken" />;
    },
    save: () => () => null,
});
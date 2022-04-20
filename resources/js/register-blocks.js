const { registerBlockType } = wp.blocks;
const { serverSideRender: ServerSideRender } = wp;

registerBlockType("owc/open-zaak", {
	title: "Zaken",
	category: "theme",
	edit: () => {
		return <ServerSideRender block="owc/open-zaak" />;
	},
	save: () => () => null,
});

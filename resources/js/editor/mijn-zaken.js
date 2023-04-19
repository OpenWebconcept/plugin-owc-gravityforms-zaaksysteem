const { registerBlockType } = wp.blocks;
const { serverSideRender: ServerSideRender } = wp;

const {
  useBlockProps,
  RichText,
  InspectorControls,
  BlockControls,
  AlignmentToolbar,
  ColorPalette,
  MediaUploadCheck,
  MediaUpload,
} = wp.blockEditor;

const { Panel, PanelBody, PanelRow, Button, TextControl, IconButton, SelectControl } =
  wp.components;

const { Fragment } = wp.element;

registerBlockType('owc/mijn-zaken', {
  apiVersion: 2,
  title: 'Mijn Zaken',
  category: 'common',
  attributes: {
    zaakClient: { type: 'string', default: 'openzaak' },
    zaaktypeFilter: { type: 'string', default: '[]' },
    updateMePlease: { type: 'boolean', default: true },
  },
  edit: ({ attributes, setAttributes }) => {
    const blockProps = useBlockProps();
    const { zaakClient, zaaktypeFilter, updateMePlease } = attributes;
    const zaaktypeFilterArr = JSON.parse(zaaktypeFilter);

    const addZTFilter = () => {
      zaaktypeFilterArr.push('');
      setAttributes({
        zaaktypeFilter: JSON.stringify(zaaktypeFilterArr),
        updateMePlease: !updateMePlease,
      });
    };

    const changeZTFilter = (ztUri, index) => {
      zaaktypeFilterArr[index] = ztUri;
      setAttributes({
        zaaktypeFilter: JSON.stringify(zaaktypeFilterArr),
        updateMePlease: !updateMePlease,
      });
    };

    const removeZTFilter = (index) => {
      zaaktypeFilterArr.splice(index, 1);
      setAttributes({
        zaaktypeFilter: JSON.stringify(zaaktypeFilterArr),
        updateMePlease: !updateMePlease,
      });
    };

    const zaaktypeFields = zaaktypeFilterArr.map((location, index) => {
      return (
        <Fragment key={index}>
          <TextControl
            className="ogz-ztfilter_add"
            placeholder="B1026"
            value={zaaktypeFilterArr[index]}
            onChange={(ztUri) => changeZTFilter(ztUri, index)}
          />
          <IconButton
            className="ogz-ztfilter_remove"
            icon="no-alt"
            label="Verwijder Zaaktype filter"
            onClick={() => removeZTFilter(index)}
          />
        </Fragment>
      );
    });

    return (
      <div {...blockProps}>
        <InspectorControls>
          <Panel>
              <PanelBody title="Zaaksysteem" initialOpen={ false }>
                <p>Selecteer het zaaksysteem waaruit de zaken opgehaald moeten worden.</p>
                <SelectControl
                    label="Zaaksysteem"
                    value={ zaakClient }
                    options={ [
                        { label: 'OpenZaak', value: 'openzaak' },
                        { label: 'Decos JOIN', value: 'decosjoin' },
                    ] }
                    onChange={ ( newzaakClient ) => setAttributes( { zaakClient: newzaakClient } ) }
                />
            </PanelBody>
            <PanelBody title="Zaaktype configuratie" initialOpen={false}>
              <PanelRow>Zaaktypes</PanelRow>
              {zaaktypeFields}

              <Button isDefault icon="plus" onClick={addZTFilter.bind(this)}>
                Voeg een Zaaktype identifier toe
              </Button>
            </PanelBody>
          </Panel>
        </InspectorControls>
        <p>Here be dragons</p>
      </div>
    );
  },
  save: ({ className, attributes }) => {
    return <section className={className}></section>;
  },
});
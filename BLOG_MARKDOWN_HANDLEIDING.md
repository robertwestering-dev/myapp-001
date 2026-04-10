# Korte Markdown-handleiding voor blogs

Gebruik in blog-posts vooral Markdown. Dat is eenvoudiger en netter dan handmatig HTML schrijven.

## Basisopmaak

### Koppen

```md
# Hoofdtitel
## Tussenkop
### Kleine tussenkop
```

### Alinea's

Laat een lege regel tussen tekstblokken:

```md
Dit is de eerste alinea.

Dit is de tweede alinea.
```

### Nadruk

```md
**vette tekst**
*schuine tekst*
```

### Lijsten

```md
- Eerste punt
- Tweede punt
- Derde punt
```

### Links

```md
[Lees meer](https://example.com)
```

## Afbeeldingen

Gebruik voor afbeeldingen gewoon Markdown:

```md
![Korte beschrijving](https://jouwdomein.nl/storage/media-assets/2026/04/afbeelding.jpg)
```

Tip:
- gebruik een duidelijke alt-tekst
- haal de afbeelding-URL uit `Beheer > Assets`

### Afbeeldingen met breedte en uitlijning

Als je meer controle wilt over de weergave, gebruik dan de `image` shortcode:

```md
[image url="https://jouwdomein.nl/storage/media-assets/2026/04/afbeelding.jpg" alt="Korte beschrijving" width="320" align="right"]
```

Ondersteunde opties:
- `url`
- `alt`
- `width`
- `height`
- `align="left|center|right"`

Voorbeelden:

```md
[image url="https://jouwdomein.nl/storage/media-assets/2026/04/teamfoto.jpg" alt="Teamoverleg" width="280" align="left"]
```

```md
[image url="https://jouwdomein.nl/storage/media-assets/2026/04/dashboard.png" alt="Dashboard screenshot" width="420" align="center"]
```

Gedrag:
- `left` en `right` laten tekst langs het beeld lopen op grotere schermen
- `center` toont de afbeelding als gecentreerd blok
- op mobiel vallen links/rechts geplaatste media automatisch terug naar een nette blokweergave

## Video's

Gebruik voor video's de shortcode uit Assetbeheer:

```md
[video url="https://jouwdomein.nl/storage/media-assets/2026/04/video.mp4"]
```

Je hoeft dus geen HTML `<video>` tag meer te typen.

### Video's met breedte en uitlijning

Je kunt voor video ook extra opties meegeven:

```md
[video url="https://jouwdomein.nl/storage/media-assets/2026/04/video.mp4" width="480" align="left"]
```

Ondersteunde opties:
- `url`
- `width`
- `height`
- `align="left|center|right"`

## Praktisch voorbeeld

```md
# Digitale rust in drukke teams

Veel teams werken de hele dag met meldingen, chats en contextwisselingen.

## Waarom dit belangrijk is

- meer focus
- minder fouten
- meer rust in samenwerking

[image url="https://jouwdomein.nl/storage/media-assets/2026/04/workshop.jpg" alt="Workshop over digitale rust" width="360" align="right"]

## Korte toelichting in video

[video url="https://jouwdomein.nl/storage/media-assets/2026/04/intro-video.mp4" width="560" align="center"]

## Conclusie

Kleine afspraken maken vaak al direct verschil.
```

## Afspraken voor deze blog

- schrijf gewone tekst in Markdown
- gebruik standaard Markdown voor eenvoudige afbeeldingen
- gebruik `[image ...]` als je breedte, hoogte of uitlijning wilt bepalen
- gebruik `[video ...]` voor video's
- plak URLs en snippets vanuit `Beheer > Assets`

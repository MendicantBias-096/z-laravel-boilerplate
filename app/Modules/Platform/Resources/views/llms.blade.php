{{--
    Mapa del sitio para agentes, en el formato de llms.txt.

    Editable a mano y a propósito: generarlo desde la tabla de rutas listaría
    también los formularios, los paneles y las pantallas de autenticación, que
    a un agente no le sirven de nada. Lo que vale es la selección, y esa es una
    decisión humana.

    Lo que se añada aquí necesita gemelo Markdown —`defaults('markdown', ...)`
    en su ruta—, y `LlmsTxtTest` falla si se enlaza algo que no lo tiene.
--}}
# {{ config('app.name') }}

> {{ __('platform::public.llms_summary') }}

## Páginas

- [{{ config('app.name') }}]({{ rtrim(route('home'), '/') }}/index.md): {{ __('platform::public.features_subtitle') }}
- [{{ __('platform::public.about_title_1') }} {{ __('platform::public.about_title_2') }}]({{ route('platform::public.about') }}.md): {{ __('platform::public.about_intro') }}

## Cómo leer esta aplicación

Cada página con gemelo responde en Markdown de dos formas, y las dos dan lo
mismo: añadiendo `.md` a su URL, o pidiéndola con la cabecera
`Accept: text/markdown`. Una página sin gemelo devuelve su HTML de siempre.

## Opcional

- [Documentación de arquitectura]({{ route('platform.docs.index') }}): 58 reglas numeradas. Requiere sesión iniciada.

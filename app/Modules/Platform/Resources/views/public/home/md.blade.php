{{--
    Gemelo Markdown de la portada.

    Usa las mismas claves de traducción que la página, así que traducir una
    cadena actualiza las dos. Un gemelo con el texto copiado se desincroniza
    el primer día y nadie lo nota, porque nadie lo mira: lo mira un agente.
--}}
# {{ __('platform::public.hero_title_1') }} {{ __('platform::public.hero_title_2') }}

{{ __('platform::public.hero_description') }}

## {{ __('platform::public.features_title') }}

{{ __('platform::public.features_subtitle') }}

- **{{ __('platform::public.feat_secure') }}** — {{ __('platform::public.feat_secure_desc') }}
- **{{ __('platform::public.feat_fast') }}** — {{ __('platform::public.feat_fast_desc') }}
- **{{ __('platform::public.feat_modular') }}** — {{ __('platform::public.feat_modular_desc') }}

## {{ __('platform::public.stack_label') }}

{{ __('platform::public.stack_subtitle') }}

---

- [{{ __('platform::public.about_title_1') }} {{ __('platform::public.about_title_2') }}]({{ route('platform::public.about') }}.md)
- [{{ __('platform::public.hero_cta') }}]({{ route('register') }})

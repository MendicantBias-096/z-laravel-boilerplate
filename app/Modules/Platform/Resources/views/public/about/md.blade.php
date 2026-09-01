{{-- Gemelo Markdown de «Nosotros». Ver la nota en home/md.blade.php. --}}
# {{ __('platform::public.about_title_1') }} {{ __('platform::public.about_title_2') }}

{{ __('platform::public.about_intro') }}

## {{ __('platform::public.about_mission_title') }}

{{ __('platform::public.about_mission_desc') }}

## {{ __('platform::public.about_vision_title') }}

{{ __('platform::public.about_vision_desc') }}

## {{ __('platform::public.about_values_title') }}

- {{ __('platform::public.about_val_quality') }}
- {{ __('platform::public.about_val_security') }}
- {{ __('platform::public.about_val_perf') }}
- {{ __('platform::public.about_val_community') }}

---

{{ __('platform::public.about_cta_desc') }}

- [{{ __('platform::public.about_cta_register') }}]({{ route('register') }})
- [{{ __('platform::public.about_cta_login') }}]({{ route('login') }})

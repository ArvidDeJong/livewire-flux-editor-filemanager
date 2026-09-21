---
title: "FAQ"
nav_order: 13
description: "Short answers about darvis/livewire-flux-editor-filemanager: what it is, what it needs, which versions it supports, whether it is safe, dead buttons."
faq: true
---

# Frequently asked questions

{% for item in site.data.faq %}
## {{ item.q }}

{{ item.a | markdownify }}
{% endfor %}

# countdown

Notes for AI assistants working on the Dotclear plugin countdown.

**Read first** the shared conventions of the author's Dotclear modules:
https://raw.githubusercontent.com/JcDenis/gh2da/master/CONVENTIONS.md
(if a web tool refuses the address, use `curl -s` on it from the shell).
The rules below take precedence for this module.

## Module

Add widget showing a countdown since/from a predefined date.

No admin page. Public side: widgets.

Required Dotclear and PHP versions: see `_define.php`.

## Structure

* `src/Backend.php`: admin side: menu entry, favorites, behaviors registration.
* `src/Frontend.php`: public side: behaviors, template tags and URL handlers registration.
* `src/My.php`: module helpers (`My::id()`, settings, permissions).
* `src/Widgets.php`: widgets definition and rendering.
* `js/`: `jquery.countdown-ar.js`, `jquery.countdown-bg.js`, `jquery.countdown-bn.js`, `jquery.countdown-bs.js`, `jquery.countdown-ca.js`, `jquery.countdown-cs.js`, `jquery.countdown-cy.js`, `jquery.countdown-da.js`, `jquery.countdown-de.js`, `jquery.countdown-el.js`, `jquery.countdown-es.js`, `jquery.countdown-et.js`, `jquery.countdown-fa.js`, `jquery.countdown-fi.js`, `jquery.countdown-fo.js`, `jquery.countdown-fr.js`, `jquery.countdown-gl.js`, `jquery.countdown-gu.js`, `jquery.countdown-he.js`, `jquery.countdown-hr.js`, `jquery.countdown-hu.js`, `jquery.countdown-hy.js`, `jquery.countdown-id.js`, `jquery.countdown-is.js`, `jquery.countdown-it.js`, `jquery.countdown-ja.js`, `jquery.countdown-kn.js`, `jquery.countdown-ko.js`, `jquery.countdown-lt.js`, `jquery.countdown-lv.js`, `jquery.countdown-mk.js`, `jquery.countdown-ml.js`, `jquery.countdown-ms.js`, `jquery.countdown-my.js`, `jquery.countdown-nb.js`, `jquery.countdown-nl.js`, `jquery.countdown-pl.js`, `jquery.countdown-pt-BR.js`, `jquery.countdown-ro.js`, `jquery.countdown-ru.js`, `jquery.countdown-sk.js`, `jquery.countdown-sl.js`, `jquery.countdown-sq.js`, `jquery.countdown-sr-SR.js`, `jquery.countdown-sr.js`, `jquery.countdown-sv.js`, `jquery.countdown-th.js`, `jquery.countdown-tr.js`, `jquery.countdown-uk.js`, `jquery.countdown-ur.js`, `jquery.countdown-uz.js`, `jquery.countdown-vi.js`, `jquery.countdown-zh-CN.js`, `jquery.countdown-zh-TW.js`, `jquery.countdown.min.js`, `jquery.plugin.min.js`.
* Widgets: `CountDown`.
* Behaviors used: `initWidgets`.

## Pitfalls

* None recorded yet: add here the traps met while working on the module.

## Testing

* Check the widgets (admin and public), in English and French.
* CI browses the common admin and public pages; extra pages can be listed in `.github/dotclear-test.txt`.

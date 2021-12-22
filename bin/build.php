<?php
/**
 * This build script was inspired from the phar-composer build script:
 * https://github.com/clue/phar-composer/blob/2848dfb76c8d9507af92973f21cf2c19a591c9d6/bin/build.php
 *
 * `bin/manuscript-entry` is a stub which is copied to manuscript firstly.
 * This is to ensure the composer install step below works with the expected bin path set in the `composer.json` file.
 * Then after the build process, the newly-created phar `manuscript` replaces that stub copy.
 */

$out = 'manuscript';

passthru('
cp bin/manuscript-entry bin/manuscript &&

rm -rf build && mkdir build &&
cp -r bin/ src/ composer.json composer.lock LICENSE build/ && rm build/bin/build.php &&

cd build &&
composer install &&
composer remove --dev symfony/framework-bundle &&
composer remove --dev symfony/var-dumper &&
composer remove --dev phpunit/phpunit &&
cd - >/dev/null &&

cd build/vendor && rm -rf */*/tests/ */*/src/tests/ */*/docs/ */*/*.md */*/composer.* */*/phpunit.* */*/.gitignore */*/.*.yml */*/*.xml && cd - >/dev/null &&
cd build/vendor/symfony/ && rm -rf */Symfony/Component/*/Tests/ */Symfony/Component/*/*.md */Symfony/Component/*/composer.* */Symfony/Component/*/phpunit.* */Symfony/Component/*/.gitignore && cd ->/dev/null &&

vendor/bin/phar-composer build build/ ' . escapeshellarg($out) . ' &&

mv ' . escapeshellarg($out) . ' bin/manuscript &&
chmod a+x bin/manuscript &&

rm -rf build

echo -n "Reported version is: " && php bin/manuscript --version', $code);
exit($code);

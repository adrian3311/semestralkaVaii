<?php

/**
 * Error view
 *
 * This template renders a simple error page for uncaught exceptions / HTTP errors.
 * It expects two variables provided by the framework:
 * - $exception: an instance of \Framework\Http\HttpException (or Throwable)
 * - $showDetail: boolean flag indicating whether to display stack traces and file/line info
 * - $view: the View helper; we use it to disable the global layout for this page
 *
 * Behavior summary:
 * - The layout is disabled (plain output) so the error can be shown on its own.
 * - The page always prints the HTTP code and the exception message in an <h1>.
 * - If $showDetail is true and the exception code is not 500, the template
 *   prints the exception class, message, file, line and full stack trace.
 * - The template then walks the exception chain (previous exceptions) and
 *   prints the same details for each previous exception when $showDetail is true.
 */

/** @var \Framework\Http\HttpException $exception */
/** @var bool $showDetail */
/** @var \Framework\Support\View $view */

// Disable the application layout: show the error as a standalone page
$view->setLayout(null);

?>

<!-- Top-level error title: HTTP code and primary exception message -->
<h1><?= $exception->getCode() . " - " . $exception->getMessage() ?></h1>

<?php
// When showing details (development mode) and the error is not a server 500, show full info
if ($showDetail && $exception->getCode() != 500) :
    ?>
    <!-- Exception summary -->
    <?= get_class($exception) ?>: <strong><?= $exception->getMessage() ?></strong>
    in file <strong><?= $exception->getFile() ?></strong>
    at line <strong><?= $exception->getLine() ?></strong>
    <!-- Full stack trace (preformatted) -->
    <pre>Stack trace:<br><?= $exception->getTraceAsString() ?></pre>
<?php endif; ?>

<?php
// If the exception references a previous exception, iterate the chain and
// show details for each previous one (only in detailed mode). This helps
// debugging nested exceptions / wrapped errors.
while ($showDetail && $exception->getPrevious() != null) { ?>
    <?= get_class($exception->getPrevious()) ?>: <strong><?= $exception->getPrevious()->getMessage() ?></strong>
    in file <strong><?= $exception->getPrevious()->getFile() ?></strong>
    at line <strong><?= $exception->getPrevious()->getLine() ?></strong>
    <pre>Stack trace:<br><?= $exception->getPrevious()->getTraceAsString() ?></pre>
    <?php $exception = $exception->getPrevious(); ?>
<?php } ?>

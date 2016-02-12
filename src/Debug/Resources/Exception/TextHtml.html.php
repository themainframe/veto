<!DOCTYPE html>
<html>
<head>
    <title>Exception</title>
    <link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>
    <style type="text/css">

        body,html {
            font-family: "pt sans", sans-serif;
            margin: 0;
            padding: 20px;
        }

        h1 {
            font-weight: bold;
            margin: 0 0 20px 0;
            font-size: 200%;
        }

        h2 {
            font-size: 120%;
            margin: 0 0 20px 0;
        }

        div.stack {
            font-family: monospace;
            background: #fafafa;
            padding: 20px;
            border: 1px solid #efefef;
            border-radius: 6px;
        }

        span.line {
            width: 20px;
            display: inline-block;
            font-weight: bold;
        }

        span.grey {
            font-family: inherit;
            color: #afafaf;
        }

    </style>
</head>
<body>
    <h1>
        <?php if ($code > 0) { ?><span class="grey"><?php print $code; ?></span>
        &nbsp; <?php } ?>
        <?php print $message; ?>
    </h1>
    <div class="stack">

        <strong><?php print $type; ?></strong> thrown at:

        <br><br>

        <?php $frameIndex = 0; ?>
        <?php foreach ($trace as $frame) { ?>
            line <strong><?php print (isset($frame['line']) ? $frame['line'] : '??'); ?></strong> of
            <?php print (isset($frame['file']) ? $frame['file'] : 'unknown file'); ?>
            <br>
            <?php print ($frame['class'] ?: ''); ?>
            <?php print ($frame['type'] ?: ''); ?>
            <?php print ($frame['function'] ?: ''); ?>()

            <?php if ($frameIndex > 0 && count($trace) > 1) { ?>
            <br><br><span class="grey">... which was called by...</span>
            <?php } ?>

            <br><br>
            <?php $frameIndex ++; ?>
        <?php } ?>
    </div>
</body>
</html>
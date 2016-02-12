<!DOCTYPE html>
<html>
<head>
    <title>Layers Debug</title>
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

    </style>
</head>
<body>
<h1>
    Layers
</h1>
<div class="stack">
    <?php foreach ($layerPriorityGroups as $priority => $layers) { ?>
        <strong>Priority level <?php print $priority; ?></strong><br />
        <?php foreach ($layers as $layerName => $layer) { ?>
            &nbsp; --> <?php print get_class($layer); ?>
        <?php } ?>
        <br /><br />
    <?php } ?>
    <?php if (0 === count($layerPriorityGroups)) { ?>
        <strong>There are no layers defined in the application.</strong>
    <?php } ?>
</div>
</body>
</html>
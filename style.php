<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/child-theme.css">
    <style>
h2 {
    border-bottom: 1px solid hsl(0 0% 0% / 0.2);
}
.data {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.output {
    display: grid;
    grid-template-columns: 200px auto ;
}
.title {
    align-self: center;
}
.value {
    width: 100%;
    min-height: 50px;
    align-self: center;
}
.single {
    width: 100%;
    min-height: 50px;
}
.colour {
    display: grid;
    grid-template-columns: repeat(4,1fr);
}
.tint {
    min-height: 25px;
}
    </style>
</head>
<body>
    <div class="container-xl">
        <h1>CSS Properties</h1>
        <div class="data">
<?php

$filePath = './src/sass/theme/_props.scss';
$fileContents = file_get_contents($filePath);

preg_match_all('/--(.*?)\s*:\s*(.*?)\s*;/m', $fileContents, $matches, PREG_SET_ORDER, 0);

$variables = array();
$colours = array();
$fsizes = array();
$fweights = array();

foreach ($matches as $match) {
    $variableName = trim($match[1]);
    $variableValue = trim($match[2]);
    $variables[$variableName] = $variableValue;

    if (preg_match('/^col/', $variableName )) {
        $colours[$variableName] = $variableValue;
    }

    if (preg_match('/^fs/', $variableName )) {
        $fsizes[$variableName] = $variableValue;
    }

    if (preg_match('/^fw/', $variableName )) {
        $fweights[$variableName] = $variableValue;
    }

}

echo '<h2>Colours</h2>';
foreach ($colours as $name => $value) {
    echo colour($name, $value);
}

echo '<h2>Font Sizes</h2>';

foreach (array_reverse($fsizes) as $name => $value) {
    echo type($name, $value);
}

echo '<h2>Font Weights</h2>';
foreach (array_reverse($fweights) as $name => $value) {
    echo weight($name, $value);
}

function colour($name, $value) {
    ob_start();
    ?>
<div class="output">
    <div class="title">--<?=$name?></div>
    <div class="value">
        <div class="single" style="background-color:var(--<?=$name?>)"></div>
    </div>
</div>
    <?php
    return ob_get_clean();
}

function type($name, $value) {
    ob_start();
    ?>
<div class="output">
    <div class="title">--<?=$name?></div>
    <div class="value" style="font-size:<?=$value?>">Lorem ipsum dolor sit amet consectetur.</div>
</div>
    <?php
    return ob_get_clean();
}

function weight($name, $value) {
    ob_start();
    ?>
<div class="output">
    <div class="title">--<?=$name?></div>
    <div class="value" style="font-size:var(--fs-400);font-weight:<?=$value?>">Lorem ipsum dolor sit amet consectetur.</div>
</div>
    <?php
    return ob_get_clean();
}

?>
        </div>
    </div>
</body>
</html>
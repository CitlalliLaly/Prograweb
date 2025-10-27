<?php
define("NUM_ALUMNOS", 5);
$estudiantes = [
    "Juan Martinez" => rand(50,100),
    "Maria Ave" => rand(50,100),
    "Carlos Más" => rand(50,100),
    "Ana Rana" => rand(50,100),
    "Luis Kilimbo" => rand(50,100)
];
function calcularPromedio($califi) {
    $suma = array_sum($califi);
    $promedio = $suma / NUM_ALUMNOS;
    return $promedio;
}
$promedio=calcularPromedio($estudiantes);

foreach($estudiantes as $nombre => $calificacion) {
    if($calificacion >= 70) {
        echo "El estudiante $nombre ha aprobado con una calificación de: $calificacion <br>";
    } else {
        echo "El estudiante $nombre ha reprobado con una calificación de: $calificacion <br>";
    }
}
echo "El promedio general de la clase es: $promedio";
?>


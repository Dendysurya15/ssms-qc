so i have result like this but how to make it be one array long. from this result to

array:1 [▼ // app\Http\Controllers\SidaktphController.php:7611
"BKE" => array:6 [▼
"OD" => array:8 [▼
0 => array:6 [▼
"est" => "BKE"
"afd" => "OD"
"status" => 1
"janjang" => 0
"brd" => 350
"luas" => 29.53
]
1 => array:6 [▼
"est" => "BKE"
"afd" => "OD"
"status" => 3
"janjang" => 5
"brd" => 98
"luas" => 68.08
]
2 => array:6 [▼
"est" => "BKE"
"afd" => "OD"
"status" => 4
"janjang" => 1
"brd" => 3863
"luas" => 95.92
]
3 => array:6 [▼
"est" => "BKE"
"afd" => "OD"
"status" => 2
"janjang" => 0
"brd" => 0
"luas" => 0
]
4 => array:6 [▼
"est" => "BKE"
"afd" => "OD"
"status" => 5
"janjang" => 0
"brd" => 0
"luas" => 0
]
5 => array:6 [▼
"est" => "BKE"
"afd" => "OD"
"status" => 6
"janjang" => 0
"brd" => 0
"luas" => 0
]
6 => array:6 [▼
"est" => "BKE"
"afd" => "OD"
"status" => 7
"janjang" => 0
"brd" => 0
"luas" => 0
]
7 => array:6 [▼
"est" => "BKE"
"afd" => "OD"
"status" => 8
"janjang" => 0
"brd" => 0
"luas" => 0
]
]
"OE" => array:8 [▶]
"OB" => array:8 [▶]
"OF" => array:8 [▶]
"OC" => array:8 [▶]
"OA" => array:8 [▶]
]
]

this result :
array:1 [▼ // app\Http\Controllers\SidaktphController.php:7611
"BKE" => array:6 [▶]
]
array:1 [▼ // app\Http\Controllers\SidaktphController.php:7611
"BKE" => array:6 [▼
"OD" => array:8 [▼
0 => array:6 [▼
"est" => "BKE"
"afd" => "OD"
"status" => 1
"janjang" => 0
"brd" => 350
"luas" => 29.53
"status2" => 3
"janjang2" => 5
"brd2" => 98
"luas2" => 68.08
"status3" => 4
"janjang3" => 1
"brd3" => 3863
"luas3" => 95.92
"status4" => 2
"janjang4" => 0
"brd4" => 0

]
]
"OE" => array:8 [▶]
"OB" => array:8 [▶]
"OF" => array:8 [▶]
"OC" => array:8 [▶]
"OA" => array:8 [▶]
]
]
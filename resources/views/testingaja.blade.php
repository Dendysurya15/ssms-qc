so i have array dataPerbulan like this:
array:12 [▼ // app\Http\Controllers\emplacementsController.php:990
"January" => array:7 [▼
"Batu Kotam" => array:1 [▼
"BKE-EST" => array:4 [▼
0 => array:14 [▼
"id" => 10
"datetime" => "2023-01-21 11:15:29"
"est" => "BKE"
"afd" => "EST"
"petugas" => "Anabel Stokes DDS"
"pendamping" => "Ms. Tamara Bradtke"
"penghuni" => "Cicero Miller"
"tipe_rumah" => "B"
"foto_temuan" => "libero.jpg$eveniet.jpg$temporibus.jpg$odit.jpg$et.jpg$nesciunt.jpg$et.jpg$illum.jpg$non.jpg$quo.jpg$beatae.jpg$aut.jpg$id.jpg"
"komentar_temuan" => "eius$error$voluptas$fugiat$non$dolor$aut$exercitationem$ut$expedita$cumque$autem$repellat"
"nilai" => "2$2$1$3$3$4$4$3$3$2$4$3$4"
"komentar" => "harum$maiores$molestiae$a$tempora$ut$amet$odit$consequatur$consequatur$ut$qui$dolores"
"bulan" => "January"
"tahun" => "2023"
]
1 => array:14 [▼
"id" => 22
"datetime" => "2023-01-21 12:15:29"
"est" => "BKE"
"afd" => "EST"
"petugas" => "Anabel Stokes DDS"
"pendamping" => "Ms. Tamara Bradtke"
"penghuni" => "Alamak"
"tipe_rumah" => "B"
"foto_temuan" => "libero.jpg$eveniet.jpg$temporibus.jpg$odit.jpg$et.jpg$nesciunt.jpg$et.jpg$illum.jpg$non.jpg$quo.jpg$beatae.jpg$aut.jpg$id.jpg"
"komentar_temuan" => "eius$error$voluptas$fugiat$non$dolor$aut$exercitationem$ut$expedita$cumque$autem$repellat"
"nilai" => "2$2$1$2$3$2$4$1$1$2$4$3$4"
"komentar" => "harum$maiores$molestiae$a$tempora$ut$amet$odit$consequatur$consequatur$ut$qui$dolores"
"bulan" => "January"
"tahun" => "2023"
]
2 => array:14 [▼
"id" => 12
"datetime" => "2023-01-22 17:43:18"
"est" => "BKE"
"afd" => "EST"
"petugas" => "Stuart Jakubowski Jr."
"pendamping" => "Prof. Nels Feil II"
"penghuni" => "Prof. Anthony Mante"
"tipe_rumah" => "C"
"foto_temuan" => "modi.jpg$molestiae.jpg$in.jpg$est.jpg$et.jpg$quia.jpg$ratione.jpg$optio.jpg$provident.jpg$et.jpg$quae.jpg$ut.jpg$quam.jpg"
"komentar_temuan" => "modi$dignissimos$perspiciatis$et$earum$quam$ab$cupiditate$sit$dolorum$ad$corrupti$delectus"
"nilai" => "3$3$4$2$2$2$2$3$1$2$4$3$1"
"komentar" => "blanditiis$dolorem$nisi$iusto$nam$quia$eos$expedita$commodi$nesciunt$omnis$occaecati$voluptas"
"bulan" => "January"
"tahun" => "2023"
]
3 => array:14 [▼
"id" => 16
"datetime" => "2023-01-26 00:37:33"
"est" => "BKE"
"afd" => "EST"
"petugas" => "Karli Runte"
"pendamping" => "Prof. Travon West"
"penghuni" => "Lupe Goyette"
"tipe_rumah" => "C"
"foto_temuan" => "distinctio.jpg$reiciendis.jpg$molestiae.jpg$voluptas.jpg$harum.jpg$enim.jpg$quibusdam.jpg$eligendi.jpg$et.jpg$in.jpg$laboriosam.jpg$maxime.jpg$nemo.jpg"
"komentar_temuan" => "corporis$expedita$aut$officiis$voluptas$ducimus$asperiores$amet$dolorum$aspernatur$cupiditate$sunt$ut"
"nilai" => "3$4$2$2$4$2$2$4$1$2$2$1$2"
"komentar" => "in$non$et$nostrum$unde$rerum$beatae$voluptatem$similique$voluptatem$quisquam$cupiditate$voluptas"
"bulan" => "January"
"tahun" => "2023"
]
]
]
"Central Workshop" => array:1 [▶]
"Kenambui" => array:1 [▶]
"Natai Baru" => array:1 [▶]
"Rungun" => array:1 [▶]
"Suayap" => array:1 [▶]
"Training Center" => array:1 [▶]
]
"February" => array:8 [▶]
"March" => 0
"April" => 0
"May" => 0
"June" => 0
"July" => 0
"August" => 0
"September" => 0
"October" => 0
"November" => 0
"December" => 0
]

in my array BKE-EST i have index 0 and 1 its same dateime but different hours ,same petugas,est,afd, and pendamping. so i want if its same on that category datetime ignore the hours ,etugas,est,afd, and pendamping i want that index 0 and 1 in BKE-EST be grouped as one array. so i want result like this:

so i have array dataPerbulan like this:
array:12 [▼ // app\Http\Controllers\emplacementsController.php:990
"January" => array:7 [▼
"Batu Kotam" => array:1 [▼
"BKE-EST" => array:4 [▼
0 => array:14 [▼
"id" => 10
"datetime" => "2023-01-21 11:15:29"
"est" => "BKE"
"afd" => "EST"
"petugas" => "Anabel Stokes DDS"
"pendamping" => "Ms. Tamara Bradtke"
"penghuni" => "Cicero Miller"
"tipe_rumah" => "B"
"foto_temuan" => "libero.jpg$eveniet.jpg$temporibus.jpg$odit.jpg$et.jpg$nesciunt.jpg$et.jpg$illum.jpg$non.jpg$quo.jpg$beatae.jpg$aut.jpg$id.jpg"
"komentar_temuan" => "eius$error$voluptas$fugiat$non$dolor$aut$exercitationem$ut$expedita$cumque$autem$repellat"
"nilai" => "2$2$1$3$3$4$4$3$3$2$4$3$4"
"komentar" => "harum$maiores$molestiae$a$tempora$ut$amet$odit$consequatur$consequatur$ut$qui$dolores"
"bulan" => "January"
"tahun" => "2023"
"penghuni1" => "Alamak"
"tipe_rumah1" => "B"
"foto_temuan1" => "libero.jpg$eveniet.jpg$temporibus.jpg$odit.jpg$et.jpg$nesciunt.jpg$et.jpg$illum.jpg$non.jpg$quo.jpg$beatae.jpg$aut.jpg$id.jpg"
"komentar_temuan1" => "eius$error$voluptas$fugiat$non$dolor$aut$exercitationem$ut$expedita$cumque$autem$repellat"
"nilai1" => "2$2$1$2$3$2$4$1$1$2$4$3$4"
"komentar1" => "harum$maiores$molestiae$a$tempora$ut$amet$odit$consequatur$consequatur$ut$qui$dolores"
]
2 => array:14 [▼
"id" => 12
"datetime" => "2023-01-22 17:43:18"
"est" => "BKE"
"afd" => "EST"
"petugas" => "Stuart Jakubowski Jr."
"pendamping" => "Prof. Nels Feil II"
"penghuni" => "Prof. Anthony Mante"
"tipe_rumah" => "C"
"foto_temuan" => "modi.jpg$molestiae.jpg$in.jpg$est.jpg$et.jpg$quia.jpg$ratione.jpg$optio.jpg$provident.jpg$et.jpg$quae.jpg$ut.jpg$quam.jpg"
"komentar_temuan" => "modi$dignissimos$perspiciatis$et$earum$quam$ab$cupiditate$sit$dolorum$ad$corrupti$delectus"
"nilai" => "3$3$4$2$2$2$2$3$1$2$4$3$1"
"komentar" => "blanditiis$dolorem$nisi$iusto$nam$quia$eos$expedita$commodi$nesciunt$omnis$occaecati$voluptas"
"bulan" => "January"
"tahun" => "2023"
]
3 => array:14 [▼
"id" => 16
"datetime" => "2023-01-26 00:37:33"
"est" => "BKE"
"afd" => "EST"
"petugas" => "Karli Runte"
"pendamping" => "Prof. Travon West"
"penghuni" => "Lupe Goyette"
"tipe_rumah" => "C"
"foto_temuan" => "distinctio.jpg$reiciendis.jpg$molestiae.jpg$voluptas.jpg$harum.jpg$enim.jpg$quibusdam.jpg$eligendi.jpg$et.jpg$in.jpg$laboriosam.jpg$maxime.jpg$nemo.jpg"
"komentar_temuan" => "corporis$expedita$aut$officiis$voluptas$ducimus$asperiores$amet$dolorum$aspernatur$cupiditate$sunt$ut"
"nilai" => "3$4$2$2$4$2$2$4$1$2$2$1$2"
"komentar" => "in$non$et$nostrum$unde$rerum$beatae$voluptatem$similique$voluptatem$quisquam$cupiditate$voluptas"
"bulan" => "January"
"tahun" => "2023"
]
]
]
"Central Workshop" => array:1 [▶]
"Kenambui" => array:1 [▶]
"Natai Baru" => array:1 [▶]
"Rungun" => array:1 [▶]
"Suayap" => array:1 [▶]
"Training Center" => array:1 [▶]
]
"February" => array:8 [▶]
"March" => 0
"April" => 0
"May" => 0
"June" => 0
"July" => 0
"August" => 0
"September" => 0
"October" => 0
"November" => 0
"December" => 0
]
# mediaExifInfo

plugin pour Dotclear

Ce plugin permet de créer une boucle sur les attachements  d'un post et de récupérer des informations exif présents dans ceux-ci.

## Points d'attention

Le plugin lit directement les informations exif dans le fichier jpg ou tif au moyen de la fonction php exif_read_data ( ). Voyez la documentation php sur la façon de compiler celui-ci pour que la fonction exif_read_data soit
prise en compte si vous compilez vous-même php.

À l'exception de la balise {{tpl:MediaExifInfoRelUrl}}, toutes les balises retournent une chaine vide si le fichier n'est pas présent sur le serveur.

Certains programmes qui permettent de modifier les données exif présentes dans un fichier ne sont pas parfaits. Je ne fais pas de modifications du plugin pour ces données erronées!

Les fichiers jpg ou tif doivent impérativement se trouver dans le répertoire 'public' de Dotclear  ou un sous-répertoire de celui-ci.

Seuls les fichiers avec une extension .jpg, .jpeg, .tif ou .tiff (en minuscules ou majuscules) sont pris en compte!

Je ne possède pas des centaines d'appareils photo ni de versions de php. Je n'ai pu tester le plugin que sur mon serveur et avec mes propres photos. La norme exif est une brochure de près de 200 pages. 
Pouvoir tout tester sort du cadre de ce plugin.

Peut-être vous demandez-vous quel est l'intérêt de ce plugin qui lit les données exif des photos attachées aux posts. Mon blog me sert à publier des photos. Dans le contenu de chaque post, je ne passe pas mon temps 
à insérer des balises html &lt;img&gt; pour chaque photo. Toute la mise en page des photos est faite dans les fichiers tpl du thème. Exemple de fichier tpl:

```
<tpl:Entries>
	<tpl:EntryIf extended="1">
		<div  class="PostExcerpt">{{tpl:EntryExcerpt}}</div>
	</tpl:EntryIf>
	<tpl:MediaExifInfos>
		<div ">
			<p>
				<img class="Picture{{tpl:MediaExifInfoClass}}" src="{{tpl:BlogURL}}{{tpl:MediaExifInfoRelUrl}}" title="<tpl:EntryCategoriesBreadcrumb>{{tpl:CategoryTitle encode_html="1"}}&#9656;</tpl:EntryCategoriesBreadcrumb>{{tpl:EntryCategory}}" />
			</p>
			<tpl:MediaExifInfoIf has_exif="1">
				<p class="PictureDate">
					{{tpl:lang PostDateText}}{{tpl:MediaExifInfoDateTimeOriginal format="%A %e %B %Y"}}
				</p>
				<p class="PictureInfo">
					<span>&#x1f4f7;</span><span>{{tpl:MediaExifInfoAllExif format="%Model% %FocalLength% mm - f %FNumber% %ExposureTime% sec. %ISOSpeedRatings% ISO"}}</span>
				</p>
			</tpl:MediaExifInfoIf>
		</div>
	</tpl:MediaExifInfos>
	<div  class="PostContent">{{tpl:EntryContent}}</div>
</tpl:Entries>
```

## Balises tpl

### &lt;tpl:MediaExifInfos&gt;

Cette balise permet de créer une boucle sur les différentes pièces jointes (attachments) d'un billet.

### &lt;tpl:MediaExifInfosHeader&gt; 

Dans le cadre d'une boucle &lt;tpl:MediaExifInfos&gt;, cette balise affiche un contenu avant les pièces jointes. 

### &lt;tpl:MediaExifInfosFooter&gt;

Dans le cadre d'une boucle &lt;tpl:MediaExifInfos&gt;, cette balise affiche un contenu après les pièces jointes. 

### &lt;tpl:MediaExifInfoIf&gt;

Dans le cadre d'une boucle &lt;tpl:MediaExifInfos&gt;, cette balise affiche un contenu répondant aux conditions précisées dans les attributs.

#### Attributs

##### has_exif

Quand cet attribut vaut "1", le contenu sera affiché uniquement si les données exif d'ouverture (FNumber), d'exposition (ExposureTime), de sensibilité ISO (ISOSpeedRatings)
et de longueur focale (FocalLength) ont été détectées.

Valeur par défaut: "0"

##### has_thumbnail

Quand cet attribut vaut "1", le contenu sera affiché uniquement si une miniature (thumbnail) est trouvée dans le répertoire où la photo est présente.

Attention: les miniatures utilisées ne suivent pas les règles habituelles de Dotclear car, pour des raisons de présentation sur mon site, les thumbnails générés automatiquement par 
Dotclear ne me conviennent pas. Une photo est considérée comme ayant une miniature si celle-ci a le même nom que la photo principale suivi de _s, suivi de l'extension et donc sans point devant le nom!

Valeur par défaut: "0"

##### is_jpg

Quand cet attribut vaut "1", le contenu sera affiché uniquement si la photo est au format jpg (extension = .jpg ou .jpeg en majuscules ou minuscules).

Valeur par défaut: "0"

##### is_tiff

Quand cet attribut vaut "1", le contenu sera affiché uniquement si la photo est au format tiff (extension = .tif ou .tiff en majuscules ou minuscules).

Valeur par défaut: "0"

### {{tpl:MediaExifInfoMimeType}}

Cette balise retourne le type mime de la photo.

Cette balise retourne une information, même si aucune donnée exif n'est présente dans la photo.

### {{tpl:MediaExifInfoSize}}

Cette balise retourne la taille du fichier jpg ou tiff.

#### Attributs

##### divisor

Un nombre par lequel la taille sera divisée avant d'être retournée. Ceci permet d'avoir une taille en o., en Ko. ou en Mo. Petit rappel: 1 Ko = 1024 o. et 1 Mo. = 1.048.576 o.

Valeur par défaut: "1"

##### format

Une chaine de caractères servant à formater la valeur retournée et suivant les règles de la commande php sprintf ( ).

Valeur par défaut: "%d"

### {{tpl:MediaExifInfoThumbnailUrl}}

Cette balise retourne l'url (relative au répertoire racine du blog) de la miniature. Si la miniature n'existe pas, une chaine vide est retournée.

### {{tpl:MediaExifInfoRelUrl}}

Cette balise retourne l'url (relative au répertoire racine du blog) de la photo. 

### {{tpl:MediaExifInfoFileName}}

Cette balise retourne le nom du fichier. 

### {{tpl:MediaExifInfoClass}}

Cette balise retourne "Landscape" ou "Portrait", selon l'orientation de la photo.

### {{tpl:MediaExifInfoExposureTime}}

Cette balise retourne le temps d'exposition de la photo, en secondes. les temps d'exposition inférieurs à la seconde sont retournés sous forme de fraction, comme il est usuel en photographie. 

#### Attributs

##### format

Une chaine de caractères servant à formater la valeur retournée et suivant les règles de la commande php sprintf ( ).

Valeur par défaut: "%s"

### {{tpl:MediaExifInfoFNumber}}

Cette balise retourne la valeur de l'ouverture sous forme de nombre, sans être précédé de f ou f/.

#### Attributs

##### format

Une chaine de caractères servant à formater la valeur retournée et suivant les règles de la commande php sprintf ( ).

Valeur par défaut: "%s"

### {{tpl:MediaExifInfoFocalLength}}

Cette balise retourne la focale utilisée pour la photo, en mm.

#### Attributs

##### format

Une chaine de caractères servant à formater la valeur retournée et suivant les règles de la commande php sprintf ( ).

Valeur par défaut: "%s"

### {{tpl:MediaExifInfoISOSpeedRatings}}

Cette balise retourne la sensibilité ISO de la photo.

#### Attributs

##### format

Une chaine de caractères servant à formater la valeur retournée et suivant les règles de la commande php sprintf ( ).

Valeur par défaut: "%s"

### {{tpl:MediaExifInfoMake}}

Cette balise retourne la marque de l'appareil photo.

### {{tpl:MediaExifInfoModel}}

Cette balise retourne la marque de l'appareil photo.

### {{tpl:MediaExifInfoDateTimeOriginal}}

Cette balise retourne la date et heure de prise de vue.

#### Attributs

##### format

Une chaine de caractères servant à formater la valeur retournée et suivant les règles de date et heure de la commande php strftime ( ).

Valeur par défaut: "%A %e %B %Y %H:%M:%S"

### {{tpl:MediaExifInfoAllExif}}

Cette balise retourne une ou plusieurs informations exif en une seule opération.

#### Attributs

##### format

Une chaine de caractères servant à formater la valeur retournée. Les règles suivantes peuvent être utilisées:

- %Make% sera remplacé par la marque de l'appareil photo

- %Model% sera remplacé par le modèle de l'appareil photo

- %FocalLength% sera remplacé par la focale utilisée

- %FNumber% sera remplacé par l'ouverture utilisée

- %ExposureTime% sera remplacé par le temps d'exposition

- %ISOSpeedRatings% sera remplacé par la sensibilité ISO

Valeur par défaut: "%Make% %Model% %FocalLength% %FNumber% %ExposureTime% %ISOSpeedRatings%"

Exemple: le format "%Make% %Model% %FocalLength% mm, f %FNumber%, %ExposureTime% sec., %ISOSpeedRatings% ISO" retournera 
"Canon Canon EOS 6D 20 mm, f 2.8, 1/90 sec., 200 ISO"
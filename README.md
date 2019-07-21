# mediaExifInfo

plugin pour Dotclear

Ce plugin permet de créer une boucle sur les attachements  d'un post et de récupérer des informations exif présents dans ceux-ci.

## Points d'attention

Le plugin lit directement les informations exif dans le fichier jpg ou tif au moyen de la fonction php exif_read_data ( ). Voyez la documentation php sur la façon de compiler celui-ci pour que la fonction exif_read_data soit
prise en compte si vous compilez vous-même php.

Certains programmes qui permettent de modifier les données exif présentes dans un fichier ne sont pas parfaits. Je ne fais pas de modifications du plugin pour ces données erronées!

Les fichiers jpg ou tif doivent impérativement se trouver dans le répertoire 'public' de Dotclear  ou un sous-répertoire de celui-ci.

Seuls les fichiers avec une extension .jpg, .jpeg, .tif ou .tiff (en minuscules ou majuscules) sont pris en compte!

Je ne possède pas des centaines d'appareils photo ni de versions de php. Je n'ai pu tester le plugin que sur mon serveur et avec mes propres photos. La norme exif est une brochure de près de 200 pages. Pouvoir tout tester sort du cadre dece plugin.

Peut-être vous demandez-vous quel est l'intérêt de ce plugin qui lit les données exif des photos attachées aux posts. Mon blog me sert à publier des photos. Dans le contenu de chaque post, je ne passe pas mon temps à insérer des éléments
html &lt;img&gt; pour chaque photo. Toute la mise en page des photos est faite dans les fichiers tpl du thème. Extrait des fichiers tpl:

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

### &lt;tpl:MediaExifInfosHeader&gt;

### &lt;tpl:MediaExifInfosFooter&gt;

### &lt;tpl:MediaExifInfoIf&gt;

#### Attributs

##### has_exif

##### has_thumbnail

##### is_jpg

##### is_tiff

### {{tpl:MediaExifInfoUrl}}

### {{tpl:MediaExifInfoFileName}}

### {{tpl:MediaExifInfoMimeType}}

### {{tpl:MediaExifInfoSize}}

#### Attributs

##### divisor

Valeur par défaut: "1"

##### format

Valeur par défaut: "%d"

### {{tpl:MediaExifInfoThumbnailUrl}}

### {{tpl:MediaExifInfoRelUrl}}

### {{tpl:MediaExifInfoClass}}

### {{tpl:MediaExifInfoExposureTime}}

#### Attributs

##### format

Valeur par défaut: "%s"

### {{tpl:MediaExifInfoFNumber}}

#### Attributs

##### format

Valeur par défaut: "%s"

#### Attributs

##### format

Valeur par défaut: "%s"

### {{tpl:MediaExifInfoFocalLength}}

#### Attributs

##### format

Valeur par défaut: "%s"

### {{tpl:MediaExifInfoISOSpeedRatings}}

#### Attributs

##### format

Valeur par défaut: "%s"

### {{tpl:MediaExifInfoMake}}

### {{tpl:MediaExifInfoModel}}

### {{tpl:MediaExifInfoDateTimeOriginal}}

#### Attributs

##### format

Valeur par défaut: "%A %e %B %Y %H:%M:%S"

### {{tpl:MediaExifInfoAllExif}}

#### Attributs

##### format

Valeur par défaut: "%Make% %Model% %FocalLength% %FNumber% %ExposureTime% %ISOSpeedRatings%"


IpBibliothequeBundle
==================

Bundle permettant d'intégrer la bibliothèque dans un site web

## Installation

### Etape 1

Pour installer ajouter ces lignes dans le fichier composer.json

```json
{
  "repositories": [{
    "type": "composer",
    "url": "https://www.repo.info-plus.fr/"
  }]
}
```

```json
{
    "require": {
        "ip/bibliothequebundle" : "^1.0"
    }
}
```

```json
{
    "config": {
        "component-dir": "web/assets"
    }
}
```

Mettre à jour les vendors

```bash
composer update
```

Et activer le bundle

```php
<?php

// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new Ip\BibliothequeBundle\IpBibliothequeBundle(),
    // ...
);
```

### Etape 2

Ajouter la configuration au fichier de configuration:

```yaml
# app/config/config.yml

ip_bibliotheque:
    assets_path: /assets
    include_assets: true
    include_bootstrap: true
    include_jQuery: true
    root_folder: /bibliotheque
    file:
        file_class: namespace_of_file_entity
    folder:
        folder_class: namespace_of_folder_entity
```

## Utilisation basique

### Comme formulaire pour une simple image

Ajouter ```IpBibliothequeType::class ``` à votre formulaire

```php
<?php

->add('file', IpBibliothequeSingleType::class, [
    'class' =>  'AppBundle:File',
    'choice_label' => 'name'
])
```

### Comme formulaire pour un envoi multiple

Créer une entité qui extends du model : ```Ip\BibliothequeBundle\Model\OrderedFile```

```php
<?php

/**
 * @ORM\Entity
 */
class MultiFile extends OrderedFile {
    /**
     * @var FileInterface file
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\File")
     * @ORM\Id
     */
    protected $file;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MultiTest")
     * @ORM\Id
     */
    protected $multiTest;

    /**
     * @return mixed
     */
    public function getMultiTest()
    {
        return $this->multiTest;
    }

    /**
     * @param mixed $multiTest
     * @return MultiFile
     */
    public function setMultiTest(MultiTest $multiTest)
    {
        $this->multiTest = $multiTest;
        return $this;
    }
}
```

Dans l'entité où vous avez votre liste de fichiers faites une liaison avec l'entité précédement créée

```php
<?php
->add('files', IpBibliothequeMultipleType::class, [
    'entry_type' => OrderedFileType::class,
    'allow_add' => true,
    'allow_delete' => true,
    'entry_options' => [
        'data_class' => MultiFile::class
    ]
]);
```

Puis dans le controller ajouter 

```php
<?php
$form = $this->createForm(MultiTestType::class, $tmulti);

$originalFiles = new ArrayCollection();
foreach ($tmulti->getFiles() as $file) {
    $originalFiles->add($file);
}
$form->handleRequest($request);
if ($form->isSubmitted() && $form->isValid()) {
    $em = $this->getDoctrine()->getManager();

    foreach ($originalFiles as $file) {
        if (false === $tmulti->getFiles()->contains($file)) {
            $tmulti->removeFile($file);
            $em->remove($file);
        }
    }

    foreach ($tmulti->getFiles() as $file){
        /** @var MultiFile $file */
        if(is_null($file->getMultiTest())){
            $file->setMultiTest($tmulti);
            $em->persist($file);
        }
    }
    $em->persist($tmulti);
    $em->flush();
    return $this->redirectToRoute('mbbedit', [
        'tmulti' => $tmulti->getId()
    ]);
}
return $this->render('default/index.html.twig', [
    'form' => $form->createView()
]);
```


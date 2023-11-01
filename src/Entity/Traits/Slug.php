<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Slug
{
    /**
     *
     * @Assert\NotBlank
     * @Groups({"read", "write"})
     *
     * @ORM\Column(name="slug", type="string",  nullable=true, unique=true)
     */
    private $slug;

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $this->slugify($slug);
    }

    public function setTitle($title){
        $this->title = $title;
        $this->setSlug($title);
    }

    public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
     //   $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }


}


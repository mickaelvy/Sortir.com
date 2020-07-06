<?php


namespace App\Entity;


class Search
{
    private $site;
    private $nom;
    private $date;
    private $dateFin;
    private $mesSorties;
    private $registered;
    private $unregistered;
    private $last;

    /**
     * @return mixed
     */
    public function getMesSorties()
    {
        return $this->mesSorties;
    }

    /**
     * @param mixed $mesSorties
     * @return Search
     */
    public function setMesSorties($mesSorties)
    {
        $this->mesSorties = $mesSorties;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * @param mixed $registered
     * @return Search
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUnregistered()
    {
        return $this->unregistered;
    }

    /**
     * @param mixed $unregistered
     * @return Search
     */
    public function setUnregistered($unregistered)
    {
        $this->unregistered = $unregistered;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLast()
    {
        return $this->last;
    }

    /**
     * @param mixed $last
     * @return Search
     */
    public function setLast($last)
    {
        $this->last = $last;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * @param mixed $dateFin
     * @return Search
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     * @return Search
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $site
     * @return Search
     */
    public function setSite($site)
    {
        $this->site = $site;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     * @return Search
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }


}
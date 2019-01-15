<?php

namespace BackendBundle\Entity;

/**
 * Likes
 */
class Likes
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \BackendBundle\Entity\Publications
     */
    private $publication;

    /**
     * @var \BackendBundle\Entity\Users
     */
    private $user;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set publication
     *
     * @param \BackendBundle\Entity\Publications $publication
     *
     * @return Likes
     */
    public function setPublication(\BackendBundle\Entity\Publications $publication = null)
    {
        $this->publication = $publication;

        return $this;
    }

    /**
     * Get publication
     *
     * @return \BackendBundle\Entity\Publications
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Set user
     *
     * @param \BackendBundle\Entity\Users $user
     *
     * @return Likes
     */
    public function setUser(\BackendBundle\Entity\Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \BackendBundle\Entity\Users
     */
    public function getUser()
    {
        return $this->user;
    }
}


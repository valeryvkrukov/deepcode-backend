<?php
namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

use AppBundle\Entity\Card;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ExclusionPolicy("all")
 */
class User extends BaseUser
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @Expose
	 */
	protected $id;

	/**
     * @ORM\Column(name="first_name", type="string", length=255)
     * @Expose
     */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string", length=255)
     * @Expose
     */
    private $lastName;

	/**
	 * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Card")
	 * @ORM\JoinTable(name="user_cards",
	 * 		joinColumns={ @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade") },
	 * 		inverseJoinColumns={ @ORM\JoinColumn(name="card_id", referencedColumnName="id", unique=true, onDelete="cascade") }
	 * )
	 * @Expose
	 */
	protected $cards;

	public function __construct()
	{
		parent::__construct();
		$this->cards = new ArrayCollection();
	}

	/**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

	/**
     * Add card
     *
     * @param AppBundle\Entity\Card $card
     *
     * @return Card
     */
	public function addCard(Card $card)
	{
		$this->cards[] = $card;
	}

    /**
     * Remove card
     *
     * @param AppBundle\Entity\Card $card
     *
     * @return Card
     */
    public function removeCard(Card $card)
    {
        $this->cards->removeElement($card);
    }

    /**
     * Get cards
     * 
     * @return ArrayCollection<Card>
     */
    public function getCards()
    {
        return $this->cards;
    }

}
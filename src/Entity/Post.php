<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[Vich\Uploadable]
#[UniqueEntity("title")]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[Assert\Length(min: 5)]
    #[Assert\NotBlank]
    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $created_at;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Image::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $images;

    private mixed $imageFiles;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'post')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;


    #[ORM\OneToOne(mappedBy: 'post', targetEntity: ImageBanner::class, cascade: ['persist', 'remove'])]
    private ?ImageBanner $imageBanner = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: LinkMedia::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $linkMedia;

    private string $urlsMedia;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable('now');
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->linkMedia = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): string
    {
        return (new Slugify())->slugify($this->title);
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setPost($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            if ($image->getPost() === $this) {
                $image->setPost(null);
            }
        }

        return $this;
    }

    public function getImageFiles(): mixed
    {
        return $this->imageFiles;
    }

    public function setImageFiles($imageFiles): self
    {
        foreach ($imageFiles as $imageFile) {
            $image = new Image();
            $image->setImageFile($imageFile);
            $this->addImage($image);
        }
        $this->imageFiles = $imageFiles;
        return $this;
    }

    public function getUrlsMedia(): string
    {
        return $this->urlsMedia;
    }

    public function setUrlsMedia(string $urlsMedia): self
    {
            $linkMedia = new LinkMedia();
            $linkMedia->setUrl($urlsMedia);
            $this->addLinkMedia($linkMedia);

        $this->urlsMedia = $urlsMedia;
        return $this;
    }


    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getImageBanner(): ?ImageBanner
    {
        return $this->imageBanner;
    }

    public function setImageBanner(ImageBanner $imageBanner): self
    {
        // set the owning side of the relation if necessary
        if ($imageBanner->getPost() !== $this) {
            $imageBanner->setPost($this);
        }

        $this->imageBanner = $imageBanner;

        return $this;
    }


    public function getLinkMedia(): Collection
    {
        return $this->linkMedia;
    }

    public function addLinkMedia(LinkMedia $linkMedia): self
    {
        if (!$this->linkMedia->contains($linkMedia)) {
            $this->linkMedia[] = $linkMedia;
            $linkMedia->setPost($this);
        }

        return $this;
    }

    public function removeLinkMedia(LinkMedia $linkMedia): self
    {
        if ($this->linkMedia->removeElement($linkMedia)) {
            // set the owning side to null (unless already changed)
            if ($linkMedia->getPost() === $this) {
                $linkMedia->setPost(null);
            }
        }

        return $this;
    }
}

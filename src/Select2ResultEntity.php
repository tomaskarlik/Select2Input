<?php

declare(strict_types = 1);

namespace TomasKarlik\Select2Input;


class Select2ResultEntity
{

	/**
	 * @var mixed
	 */
	private $id;

	/**
	 * @var Select2ResultEntity[]
	 */
	private $children = [];

	/**
	 * @var array<string, mixed>
	 */
	private $customParams = [];

	/**
	 * @var string
	 */
	private $text;

	/**
	 * @var bool
	 */
	private $selected = FALSE;


	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * @param mixed $id
	 */
	public function setId($id): void
	{
		$this->id = $id;
	}


	public function getText(): string
	{
		return $this->text;
	}


	public function setText(string $text): void
	{
		$this->text = $text;
	}


	public function isSelected(): bool
	{
		return $this->selected;
	}


	public function setSelected(bool $selected): void
	{
		$this->selected = $selected;
	}


	public function addChildren(Select2ResultEntity $entity): void
	{
		$this->children[] = $entity;
	}


	/**
	 * @return Select2ResultEntity[]
	 */
	public function getChilds(): array
	{
		return $this->children;
	}


	public function hashChilds(): bool
	{
		return (bool) count($this->children);
	}


	/**
	 * @param mixed $value
	 */
	public function addCustomParam(string $name, $value): void
	{
		$this->customParams[$name] = $value;
	}


	/**
	 * @return array<string, mixed>
	 */
	public function getCustomParams(): array
	{
		return $this->customParams;
	}


	public function hashCustomParams(): bool
	{
		return (bool) count($this->customParams);
	}


	public function __toString(): string
	{
		return $this->text;
	}

}

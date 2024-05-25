<?php

namespace App\Shared\Builder;

class WhereFilter
{
    public string $whereClause;
    public static function asFilter(): WhereFilter
    {
        $self = new self();
        $self->whereClause = '';
        return $self;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return $this
     */
    public function withStringParameter(string $property, mixed $value): static
    {
        if (empty($value)) {
            return $this;
        }
        if (!empty($this->whereClause)) {
            $this->whereClause .= 'AND'. $property . '=' ."'".$value."'";
        }
        if (empty($this->whereClause)) {
            $this->whereClause = $property.'='."'".$value."'";
        }
        return $this;
    }

    /**
     * @return string
     */
    public function build(): string
    {
        return $this->whereClause;
    }
}

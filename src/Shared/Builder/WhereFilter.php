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
    public function withParameter(string $property, mixed $value): static
    {
        if (empty($value)) {
            return $this;
        }
        $clause = $property.'='."'".$value."'";
        if (!empty($this->whereClause)) {
            $this->whereClause .= ' AND '. $clause;
        }
        if (empty($this->whereClause)) {
            $this->whereClause = $clause;
        }
        return $this;
    }

    public function withDateParameter(string $property, ?string $date): static
    {
        if (empty($date)) return $this;
        $clause = 'DATE('.$property.')'. '=' . "'".$date."'";
        if (!empty($this->whereClause)) {
            $this->whereClause .= ' AND '. $clause;
        }
        if (empty($this->whereClause)){
            $this->whereClause = $clause;
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

    public function withFunctionParameter(string $function, string $field, ?int $value): static
    {
        if (empty($value)) return $this;
        $clause = $function."(".$field.")=".$value;
        if (!empty($this->whereClause)){
            $this->whereClause.= ' AND '.$clause;
        }
        if (empty($this->whereClause)){
            $this->whereClause = $clause;
        }
        return $this;
    }
}

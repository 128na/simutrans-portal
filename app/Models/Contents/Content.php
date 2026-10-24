<?php

declare(strict_types=1);

namespace App\Models\Contents;

abstract class Content
{
    public ?int $thumbnail;

    /**
     * @param  array{thumbnail?:int|null,sections?:array<int,array{type:string,caption?:string,text?:string,url?:string,id?:int}>,markdown?:string,description?:string|null,file?:int|null,author?:string|null,license?:string|null,thanks?:string|null,link?:string,agreement?:bool,exclude_link_check?:bool}  $contents
     */
    public function __construct(array $contents)
    {
        $id = $contents['thumbnail'] ?? null;
        $this->thumbnail = $id ? (int) $id : null;
    }

    abstract public function getDescription(): string;
}

<?php

declare(strict_types=1);

namespace App\Models\Contents;

abstract class Content
{
    public null|int $thumbnail;

    /**
     * @param  array{thumbnail?:int,sections?:array<int,array{type:string,caption?:string,text?:string,url?:string,id?:int}>,markdown?:string,description?:string,file?:int,author?:string,license?:string,thanks?:string,link?:string,agreement?:bool,exclude_link_check?:bool}  $contents
     */
    public function __construct(array $contents)
    {
        $id = $contents['thumbnail'] ?? null;
        $this->thumbnail = $id ? (int) $id : null;
    }

    abstract public function getDescription(): string;
}

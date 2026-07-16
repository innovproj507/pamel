<?php

namespace Plugins\Seo\Models;

use Core\Model;

class MetaTag extends Model
{
    protected $table = 'meta_tags';

    public function findByUrl($url)
    {
        return $this->db->fetchOne("SELECT * FROM {$this->table} WHERE page_url = ?", [$url]);
    }

    public function upsert($url, $data)
    {
        $existing = $this->findByUrl($url);
        
        $data['page_url'] = $url;
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            return $this->create($data);
        }
    }

    public function getAllPages()
    {
        return $this->all();
    }
}

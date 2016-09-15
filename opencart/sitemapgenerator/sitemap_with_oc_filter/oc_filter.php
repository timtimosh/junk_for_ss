 public function callback_sitemapxml($settings) {
   
        $this->construct($settings);

        $json = array();

        $json['total'] = $this->products_total;
        $json['text_total'] = $this->declOfNum($this->products_total, array($this->language->get('button_show_total_1'), $this->language->get('button_show_total_2'), $this->language->get('button_show_total_3')));

        $json['values'] = array();

        foreach ($this->getOCFilterOptions() as $option) {
            if ($option['type'] == 'slide' || $option['type'] == 'slide_dual') {
                continue;
            }

            if ($option['type'] == 'select' || $option['type'] == 'radio') {
                $params = $this->cancelOptionParams($option['option_id']);

                $href = $this->getParamsHref($params);

                $json['values']['cancel-' . $option['option_id']] = array(
                    't' => 1,
                    'h' => $href,
                    's' => false
                );
            }

            foreach ($option['values'] as $value) {
                $json['values'][$value['id']] = array(
                    't' => $value['count'],
                    'h' => $value['href'],
                    's' => isset($this->options_get[$option['option_id']][$value['value_id']])
                );
            }
        }

        $json['href'] = $this->getParamsHref($this->params);
       
        echo json_encode($json);
    }


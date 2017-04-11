<?php
/**
* 2010-2017 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkUvdeskHelper extends ObjectModel
{
    public $uvdesk_access_token;
    public $uvdesk_company_domain;

    public function __construct($dataconfig = false)
    {
        $this->uvdesk_access_token = Configuration::get('WK_UVDESK_ACCESS_TOKEN');
        $this->uvdesk_company_domain = Configuration::get('WK_UVDESK_COMPANY_DOMAIN');
    }

    /**
     * Get All tickets for all data OR for a specific data ie. by page, by label, by agent, by customer etc
     *
     * @param array $data - collection page specific data
     * @return object response
     */
    public function getTickets($data = array())
    {
        // Return tickets
        $url = 'tickets.json?';
        $url .= 'page=' . $data['page'];
        if (isset($data['status']) && $data['status']) {
            $url .= '&status=' . $data['status'];
        }
        if (isset($data['label']) && $data['label']) {
            $url .= '&'. $data['label'];
        }
        if (isset($data['custom_label']) && $data['custom_label']) {
            $url .= '&label='. $data['custom_label'];
        }
        if (isset($data['search']) && $data['search']) {
            $url .= '&search='. $data['search'];
        }
        if (isset($data['customer']) && $data['customer']) {
            $url .= '&customer='. $data['customer'];
        }
        if (isset($data['agent']) && $data['agent']) {
            $url .= '&agent='. $data['agent'];
        }
        if (isset($data['priority']) && $data['priority']) {
            $url .= '&priority='. $data['priority'];
        }
        if (isset($data['group']) && $data['group']) {
            $url .= '&group='. $data['group'];
        }
        if (isset($data['team']) && $data['team']) {
            $url .= '&team='. $data['team'];
        }
        if (isset($data['type']) && $data['type']) {
            $url .= '&type='. $data['type'];
        }
        if (isset($data['tag']) && $data['tag']) {
            $url .= '&tag='. $data['tag'];
        }
        if (isset($data['mailbox']) && $data['mailbox']) {
            $url .= '&mailbox='. $data['mailbox'];
        }
        if (isset($data['sort']) && $data['sort']) {
            $url .= '&sort=' . $data['sort'];
        }
        if (isset($data['order']) && $data['order']) {
            $url .= '&direction=' . $data['order'];
        }
        $tickets = $this->callApi($url);
        return $tickets;
    }

    /**
     * For getting ticket we always use increment Id
     *
     * @param type $incrementId - Increment Id is a ticket id for a particular company
     *
     * @return object response
     */
    public function getTicket($incrementId)
    {
        // Returns ticket
        $url = 'ticket/' . $incrementId . '.json';
        $ticket = $this->callApi($url);
        return $ticket;
    }

    /**
     * Create ticket by customer with a subject and message
     *
     * @param array $data - collection of customer name, email, subject and reply
     *
     * @return object response
     */
    public function createTicket($data)
    {
        $url = 'tickets.json';
        $ticket = $this->postApi($url, $data, 'POST');
        return $ticket;
    }

    /**
     * Delete customer tickets by Admin
     *
     * @param array $ticketIds - collection of ticket ids
     *
     * @return object response
     */
    public function deleteTickets($ticketIds)
    {
        $url = 'tickets.json';
        $data = array(
            'ids' => $ticketIds
            );
        $response = $this->postApi($url, $data, 'DELETE');
        return $response;
    }

    /**
     * Get customer details at uvdesk by customer email id
     *
     * @param string $customerEmail - customer email
     *
     * @return object response
     */
    public function getCustomerByEmail($customerEmail)
    {
        $url = 'customers.json?';
        $url .= 'email=' . $customerEmail;

        $customer = $this->callApi($url);
        return $customer;
    }

    /**
     * Get Customers details by matched string name
     *
     * @param string $name - searched string name
     *
     * @return object response
     */
    public function getCustomers($name)
    {
        $url = 'customers.json?search=' . $name;
        $ticket = $this->callApi($url);
        return $ticket;
    }

    /**
     * Get members (agents) details by searched name (If no name exist then it will return all members details)
     *
     * @param string|bool $name - searched name
     *
     * @return object response
     */
    public function getMembers($name = false)
    {
        if ($name) {
            $url = 'members.json?search='.$name;
        } else {
            $url = 'members.json?fullList=1';
        }
        $ticket = $this->callApi($url);
        return $ticket;
    }

    /**
     * Add a collaboration by a specific ticket by customer
     *
     * @param int $ticketId - ticket id
     * @param string $email - collaboration email id
     *
     * @return object response
     */
    public function addCollaborator($ticketId, $email)
    {
        $url = 'ticket/'.$ticketId.'/collaborator.json';
        $data = array(
                'email' => $email
            );
        $response = $this->postApi($url, $data, 'POST');
        return $response;
    }

    /**
     * Remove collaboration from the ticket
     *
     * @param int $ticketId - ticket id
     * @param int $collaboratorId - collaboration id
     *
     * @return object response
     */
    public function removeCollaborator($ticketId, $collaboratorId)
    {
        $url = 'ticket/'.$ticketId.'/collaborator.json';
        $data = array(
                'collaboratorId' => $collaboratorId
            );
        $response = $this->postApi($url, $data, 'DELETE');
        return $response;
    }

    /**
     * Get tags for tickets
     *
     * @param string $name
     *
     * @return object response
     */
    public function getTags($name)
    {
        $url = 'tags.json?search=' . $name;
        $ticket = $this->callApi($url);
        return $ticket;
    }

    /**
     * Get all threads of a specific ticket
     *
     * @param int $ticketId - ticket id
     * @param int|bool $page - page number
     *
     * @return object response
     */
    public function getThreads($ticketId, $page = false)
    {
        $url = 'ticket/' . $ticketId . '/threads.json';
        if ($page) {
            $url .= '?page=' . $page;
        }
        $threads = $this->callApi($url);
        return $threads;
    }

    /**
     * Add thread on a speicific ticket
     *
     * @param int $ticketId - ticket id
     * @param string $reply - reply by customer or agent
     * @param stirng $actAsType - act as customer or agent
     *
     * @return object response
     */
    public function addThread($ticketId, $reply, $actAsType)
    {
        $url = 'ticket/' . $ticketId . '/threads.json';
        $lineEnd = "\r\n";
        $mimeBoundary = md5(time());
        $data = '--' . $mimeBoundary . $lineEnd;
        $data .= 'Content-Disposition: form-data; name="reply"' . $lineEnd . $lineEnd;
        $data .= $reply . $lineEnd;
        $data .= '--' . $mimeBoundary . $lineEnd;
        $data .= 'Content-Disposition: form-data; name="threadType"' . $lineEnd . $lineEnd;
        $data .= "reply" . $lineEnd;
        $data .= '--' . $mimeBoundary . $lineEnd;
        // attachements
        
        // act as type (type of user making reply to differentiate whether the user is customer or agent)
        $data .= 'Content-Disposition: form-data; name="actAsType"' . $lineEnd . $lineEnd;
        $data .= "".$actAsType."" . $lineEnd;
        $data .= '--' . $mimeBoundary . $lineEnd;

        if ($actAsType == 'customer') {
            //$customerEmail = Context::getContext()->customer->email;
            $customerEmail = 'neeraj751@webkul.com';
            // act as email (email of user making reply to differentiate whether the reply is made by the customer or collaborator)
            $data .= 'Content-Disposition: form-data; name="actAsEmail"' . $lineEnd . $lineEnd;
            $data .= "".$customerEmail."" . $lineEnd;
            $data .= '--' . $mimeBoundary . $lineEnd;
        }

        $attachmentFile = $_FILES['attachment'];
        if (isset($attachmentFile) && $attachmentFile) {
            foreach ($attachmentFile['name'] as $key => $file) {
                $fileType = $attachmentFile['type'][$key];
                $fileName = $attachmentFile['name'][$key];
                $fileTmpName = $attachmentFile['tmp_name'][$key];
                $data .= 'Content-Disposition: form-data; name="attachments[]"; filename="' . $fileName . '"' . $lineEnd;
                $data .= "Content-Type: $fileType" . $lineEnd . $lineEnd;
                $data .= Tools::file_get_contents($fileTmpName) . $lineEnd;
                $data .= '--' . $mimeBoundary . $lineEnd;
            }
        }
        $data .= "--" . $mimeBoundary . "--" . $lineEnd . $lineEnd;
        $response = $this->postApi($url, $data, 'POST', $mimeBoundary);
        return $response;
    }

    /**
     * Get Filter data according to choice ie. agent, customer etc.
     *
     * @param string $data - string ie. agent, customer
     *
     * @return object response
     */
    public function getFilteredData($data)
    {
        $url = 'filters.json?'.$data.'=1';
        
        $response = $this->callApi($url);
        return $response;
    }

    /**
     * Assign ticket to any agent (member)
     *
     * @param int $ticketId  - ticket id
     * @param int $memberId  - member id
     *
     * @return object response
     */
    public function assignAgent($ticketId, $memberId)
    {
        $url = 'ticket/' . $ticketId . '/agent.json';
        $data = array(
            'id' => $memberId
            );
        $response = $this->postApi($url, $data, 'PUT');
        return $response;
    }

    /**
     * Download file of any thread
     *
     * @param int $attachmentId - attachment id
     *
     * @return object response
     */
    public function downloadAttachment($attachmentId)
    {
        if ($attachmentId) {
            $companyDomain = $this->uvdesk_company_domain;
            $accessToken = $this->uvdesk_access_token;
            $downloadURL = 'https://' . $companyDomain . '.uvdesk.com/en/api/ticket/attachment/' . $attachmentId . '.json?access_token=' . $accessToken;

            return $downloadURL;
        }
    }

    /**
     * Call api for getting information
     *
     * @param string $addedUrl - url that want to hit
     *
     * @return object response
     */
    private function callApi($addedUrl = '')
    {
        $companyDomain = $this->uvdesk_company_domain;
        $url = 'https://' . $companyDomain . '.uvdesk.com/en/api/';
        $url .= $addedUrl;
        $accessToken = $this->uvdesk_access_token;
        $ch = curl_init($url);
        $headers = array(
            'Authorization: Bearer ' . $accessToken,
        );
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = Tools::substr($output, 0, $header_size);
        $response = Tools::substr($output, $header_size);
        if ($info['http_code'] == 200) {
            return json_decode($response);
        } elseif ($info['http_code'] == 404) {
            curl_close($ch);
            return array(
                'error' => 1,
                'description' => 'Error, resource not found (http-code: 404)'
                );
        } else {
            curl_close($ch);
            return json_decode($response);
        }
        curl_close($ch);
        exit();
    }

    /**
     * Call api for posting information
     *
     * @param type|string $addedUrl
     * @param type $data
     * @param type|string $custom
     * @param type|string $mimeBoundary
     *
     * @return object response
     */
    protected function postApi($addedUrl = '', $data, $custom = '', $mimeBoundary = '')
    {
        $accessToken = $this->uvdesk_access_token;
        // ticket url
        $companyDomain = $this->uvdesk_company_domain;
        $url = 'https://' . $companyDomain . '.uvdesk.com/en/api/';
        $url .= $addedUrl;
        if (!$mimeBoundary) {
            $data = json_encode($data);
        }
        $ch = curl_init($url);
        if ($mimeBoundary) {
            $headers = array(
                "Authorization: Bearer ".$accessToken,
                "Content-type: multipart/form-data; boundary=" . $mimeBoundary,
            );
        } else {
            $headers = array(
                'Authorization: Bearer ' . $accessToken,
                'Content-type: application/json'
            );
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($custom) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $custom);
        }
        $server_output = curl_exec($ch);
        $info = curl_getinfo($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = Tools::substr($server_output, 0, $header_size);
        $response = Tools::substr($server_output, $header_size);
        if ($info['http_code'] == 200 || $info['http_code'] == 201) {
            curl_close($ch);
            return json_decode($response);
        } elseif ($info['http_code'] == 400) {
            curl_close($ch);
            return json_decode($response);
        } elseif ($info['http_code'] == 404) {
            echo "Error, resource not found (http-code: 404) \n";
        } else {
            echo "Error, HTTP Status Code : " . $info['http_code'] . "\n";
            echo "Headers are ".$headers;
            echo "Response are ".$response;
        }
        curl_close($ch);
        exit();
    }

    /**
     * Get products page by page according to pagination
     *
     * @param type|null $total_products
     *
     * @return array
     */
    public function pagination($total_products = null)
    {
        $this->context = Context::getContext();

        // Retrieve the default number of products per page and the other available selections
        $default_products_per_page = max(1, (int)Configuration::get('PS_PRODUCTS_PER_PAGE'));
        $n_array = array($default_products_per_page, $default_products_per_page * 2, $default_products_per_page * 5);

        if ((int)Tools::getValue('n') && (int)$total_products > 0) {
            $n_array[] = $total_products;
        }
        // Retrieve the current number of products per page (either the default, the GET parameter or the one in the cookie)
        $this->n = $default_products_per_page;
        if (isset($this->context->cookie->nb_item_per_page) && in_array($this->context->cookie->nb_item_per_page, $n_array)) {
            $this->n = (int)$this->context->cookie->nb_item_per_page;
        }

        if ((int)Tools::getValue('n') && in_array((int)Tools::getValue('n'), $n_array)) {
            $this->n = (int)Tools::getValue('n');
        }

        // Retrieve the page number (either the GET parameter or the first page)
        $this->p = (int)Tools::getValue('p', 1);
        // If the parameter is not correct then redirect (do not merge with the previous line, the redirect is required in order to avoid duplicate content)
        if (!is_numeric($this->p) || $this->p < 1) {
            Tools::redirect($this->context->link->getPaginationLink(false, false, $this->n, false, 1, false));
        }

        // Remove the page parameter in order to get a clean URL for the pagination template
        $current_url = preg_replace('/(?:(\?)|&amp;)p=\d+/', '$1', Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']));

        if ($this->n != $default_products_per_page || isset($this->context->cookie->nb_item_per_page)) {
            $this->context->cookie->nb_item_per_page = $this->n;
        }

        $pages_nb = ceil($total_products / (int)$this->n);
        if ($this->p > $pages_nb && $total_products != 0) {
            Tools::redirect($this->context->link->getPaginationLink(false, false, $this->n, false, $pages_nb, false));
        }

        $range = 2; /* how many pages around page selected */
        $start = (int)($this->p - $range);
        if ($start < 1) {
            $start = 1;
        }

        $stop = (int)($this->p + $range);
        if ($stop > $pages_nb) {
            $stop = (int)$pages_nb;
        }

        $this->context->smarty->assign(array(
            'nb_products'       => $total_products,
            'products_per_page' => $this->n,
            'pages_nb'          => $pages_nb,
            'p'                 => $this->p,
            'n'                 => $this->n,
            'nArray'            => $n_array,
            'range'             => $range,
            'start'             => $start,
            'stop'              => $stop,
            'current_url'       => $current_url,
        ));
    }
}

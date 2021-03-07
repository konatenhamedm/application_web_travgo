<?php



namespace App\Service\Twig;



use App\Service\Breadcrumb;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Twig\Extension\AbstractExtension;

use Twig\TwigFilter;

use Twig\TwigFunction;

use Twig\TwigTest;



class _AppExtension extends AbstractExtension

{

    /**

     * @var mixed

     */

    private $breadcrumb;



    /**

     * @var mixed

     */

    private $router;



    /**

     * @var mixed

     */

    private $templating;



    /**

     * @var mixed

     */

    private $token;



    /**

     * @var mixed

     */

    private $em;



    /**

     * @param Breadcrumb $breadcrumb

     * @param $attachmentsDir

     */

    public function __construct(Breadcrumb $breadcrumb, RouterInterface $router, \Twig_Environment $templating, TokenStorageInterface $token, EntityManagerInterface $em)

    {

        $this->breadcrumb = $breadcrumb;

        $this->router     = $router;

        $this->templating = $templating;

        $this->token      = $token;

        $this->em         = $em;

    }



    public function getFilters()

    {



        return [

            // the logic of this filter is now implemented in a different class

            new TwigFilter('time_ago', [$this, 'timeAgo']),

            new TwigFilter('remaining', [$this, 'remaining']),

            new TwigFilter('truncate', [$this, 'truncate']),

            new TwigFilter('format_size', [$this, 'formatSize']),



        ];

    }



    public function getTests()

    {



        return [

            // the logic of this filter is now implemented in a different class

            new TwigTest('datetime', [$this, 'isDate']),

            new TwigTest('time', [$this, 'isTime']),



        ];

    }



    public function getFunctions()

    {

        return [

            // the logic of this filter is now implemented in a different class



            new TwigFunction('get_days', [$this, 'getDays']),

            new TwigFunction('random_str', [$this, 'randomStr']),



            new TwigFunction('render_breadcrumb', [$this, 'renderBreadcrumb']),



            new TwigFunction('set_text', [$this, 'setText']),

            //new TwigFunction('set_attribute', [$this, 'setAttribute']),



        ];

    }



    /**

     * @param $value

     * @return mixed

     */

    public function isDate($value)

    {

        return $value instanceof \DateTimeInterface;

    }



    /**

     * @param $value

     * @return mixed

     */

    public function isTime($value)

    {

        return $this->isDate($value) && $value->format('Y-m-d') == '1970-01-01';

    }



    /**

     * @param $string

     * @param $length

     * @param $etc

     * @param $break_words

     * @param false $middle

     * @return mixed

     */

    public function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)

    {

        if ($length == 0) {

            return '';

        }



        if (true) {

            if (mb_strlen($string, 'utf-8') > $length) {

                $length -= min($length, mb_strlen($etc, 'utf-8'));

                if (!$break_words && !$middle) {

                    $string = preg_replace('/\s+?(\S+)?$/u', '',

                        mb_substr($string, 0, $length + 1, 'utf-8'));

                }

                if (!$middle) {

                    return mb_substr($string, 0, $length, 'utf-8') . $etc;

                }



                return mb_substr($string, 0, $length / 2, 'utf-8') . $etc .

                mb_substr($string, -$length / 2, $length, 'utf-8');

            }



            return $string;

        }



        // no MBString fallback

        if (isset($string[$length])) {

            $length -= min($length, strlen($etc));

            if (!$break_words && !$middle) {

                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));

            }

            if (!$middle) {

                return substr($string, 0, $length) . $etc;

            }



            return substr($string, 0, $length / 2) . $etc . substr($string, -$length / 2);

        }



        return $string;

    }



    /**

     * @param $items

     * @return mixed

     */

    public function renderBreadcrumb($items)

    {

        $this->breadcrumb->addItems($items);



        return $this->breadcrumb->render();

    }



    /**

     * @param array $modules

     * @return mixed

     */

    public function flipModule(array $modules)

    {

        $results = [];

        foreach ($modules as $label => $route) {

            if (!is_array($route)) {

                $results[$route] = $label;

            } else {

                $results[$route['name']] = $label;

            }

        }



        return $results;

    }



    /**

     * @param $date

     * @param $delai

     */

    public function remaining($date, $delai)

    {

        $now = new \DateTimeImmutable();

        $date->modify("+${delai} hour");

        if ($now > $date) {

            return 'expiré';

        }



        $diff = $now->diff($date);



        $tpl = '';



        if ($diff->d > 0) {

            $tpl .= $diff->h . 'j ';

        }



        if ($diff->h > 0) {

            $tpl .= $diff->h . 'h ';

        }



        if ($diff->i > 0) {

            $tpl .= $diff->i . ' min ';

        }



        return $tpl;

    }



    /**

     * @param $date

     * @return mixed

     */

    public function timeAgo($date)

    {



        //$dateTime = new \DateTimeImmutable($date);

        $now = new \DateTimeImmutable();



        if (!$date instanceof \DateTimeInterface) {

            $date = new \DateTimeImmutable($date);

        }



        $diff = $now->diff($date);



        if ($diff->d >= 1) {

            if ($date->format('H:i:s') != '00:00:00') {

                return $date->format('d/m/Y à H:i:s');

            }



            return $date->format('d/m/Y');



        }



        $tpl = '';



        if ($diff->h > 0) {

            $tpl .= $diff->h . 'h ';

        }



        if ($diff->i > 0) {

            $tpl .= $diff->i . ' min ';

        }



        return 'Il y a ' . $tpl;



    }



    /**

     * @param $d1

     * @param $d2

     * @return mixed

     */

    public function getDays($d1, $d2)

    {

        if ($d1 && $d2) {

            if (!$d1 instanceof \DateTimeInterface) {

                $d1 = date_create(str_replace('/', '-', $d1));

            }



            if (!$d2 instanceof \DateTimeInterface) {

                $d2 = date_create(str_replace('/', '-', $d2));

            }



            if ($d1 && $d2) {

                $diff = date_diff($d1, $d2);



                return $diff->d;

            }



        }



    }



    /**

     * @param $role

     * @param $currentText

     * @param $altText

     * @return mixed

     */

    public function setText($role, $currentText, $altText)

    {

        if ($this->token->getToken()->getUser()->hasRole($role)) {

            return $currentText;

        }



        return $altText;

    }



    /**

     * @param $length

     * @return mixed

     */

    public function randomStr($len = 8)

    {



        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWZYZabcdefghijklmnopqrstuvwxyz';



        $alphamax = strlen($alphabet) - 1;

        /**

         * @var string

         */

        static $str = '';

        for ($i = 0; $i < $len; ++$i) {

            $str .= $alphabet[random_int(0, $alphamax)];

        }



        return strtolower($str);

    }

}


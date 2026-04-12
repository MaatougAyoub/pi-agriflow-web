<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* base.html.twig */
class __TwigTemplate_937d3d052e676665fd07d87538bc52b0 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'stylesheets' => [$this, 'block_stylesheets'],
            'sidebar' => [$this, 'block_sidebar'],
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "base.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "base.html.twig"));

        // line 1
        yield "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <title>";
        // line 5
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield "</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { display: flex; min-height: 100vh; background: #F5F6FA; }

        /* SIDEBAR */
        .sidebar { width: 260px; background: #1B4332; padding: 0; min-height: 100vh; position: fixed; display: flex; flex-direction: column; }
        .sidebar .logo-area { padding: 30px 20px; display: flex; flex-direction: column; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar .logo-icon { font-size: 48px; margin-bottom: 8px; }
        .sidebar .logo-text { color: white; font-size: 18px; font-weight: 800; letter-spacing: 2px; }
        .sidebar nav { padding: 20px 0; flex: 1; }
        .sidebar a { display: flex; align-items: center; gap: 12px; padding: 14px 25px; color: rgba(255,255,255,0.75); text-decoration: none; font-size: 15px; font-weight: 500; transition: all 0.2s; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }
        .sidebar a.active { background: rgba(255,255,255,0.15); color: white; border-left: 4px solid #52B788; }
        .sidebar a .icon { font-size: 18px; width: 24px; text-align: center; }

        /* MAIN */
        .main { margin-left: 260px; padding: 35px 40px; width: calc(100% - 260px); min-height: 100vh; }
        .page-title { font-size: 26px; font-weight: 800; color: #1B4332; }
        .page-subtitle { font-size: 13px; color: #95a5a6; margin-bottom: 8px; }

        /* BUTTONS */
        .btn { display: inline-block; padding: 10px 22px; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; border: none; font-size: 14px; }
        .btn-green { background: #1B4332; color: white; }
        .btn-green:hover { background: #2D6A4F; }
        .btn-blue { background: #1976D2; color: white; }
        .btn-gray { background: #f0f0f0; color: #333; }
        .btn-red { background: #e74c3c; color: white; }

        /* CARDS */
        .card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }

        /* TABLE */
        table { width: 100%; border-collapse: collapse; }
        table th { background: #f8f9fa; padding: 13px 15px; text-align: left; font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        table td { padding: 13px 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        table tr:hover { background: #fafafa; }

        /* BADGES */
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-green { background: #D8F3DC; color: #1B4332; }
        .badge-blue { background: #E3F2FD; color: #1976D2; }
        .badge-orange { background: #FFF3E0; color: #E65100; }
        .badge-red { background: #FFEBEE; color: #C62828; }

        /* FORMS */
        form label { display: block; font-weight: 600; color: #555; margin-bottom: 5px; margin-top: 15px; font-size: 14px; }
        form input, form select, form textarea { width: 100%; padding: 11px 14px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: border 0.2s; }
        form input:focus, form select:focus, form textarea:focus { border-color: #1B4332; outline: none; }
        form textarea { height: 120px; resize: vertical; }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    </style>
    ";
        // line 58
        yield from $this->unwrap()->yieldBlock('stylesheets', $context, $blocks);
        // line 59
        yield "</head>
<body>
    <div class=\"sidebar\">
        <div class=\"logo-area\">
            <div class=\"logo-icon\">💧</div>
            <div class=\"logo-text\">AGRIFLOW</div>
        </div>
        <nav>
            ";
        // line 67
        yield from $this->unwrap()->yieldBlock('sidebar', $context, $blocks);
        // line 68
        yield "        </nav>
    </div>
    <div class=\"main\">
        ";
        // line 71
        yield from $this->unwrap()->yieldBlock('body', $context, $blocks);
        // line 72
        yield "    </div>
</body>
</html>";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 5
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        yield "AGRIFLOW";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 58
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_stylesheets(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 67
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_sidebar(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "sidebar"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "sidebar"));

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 71
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_body(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "base.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  215 => 71,  193 => 67,  171 => 58,  148 => 5,  135 => 72,  133 => 71,  128 => 68,  126 => 67,  116 => 59,  114 => 58,  58 => 5,  52 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <title>{% block title %}AGRIFLOW{% endblock %}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { display: flex; min-height: 100vh; background: #F5F6FA; }

        /* SIDEBAR */
        .sidebar { width: 260px; background: #1B4332; padding: 0; min-height: 100vh; position: fixed; display: flex; flex-direction: column; }
        .sidebar .logo-area { padding: 30px 20px; display: flex; flex-direction: column; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar .logo-icon { font-size: 48px; margin-bottom: 8px; }
        .sidebar .logo-text { color: white; font-size: 18px; font-weight: 800; letter-spacing: 2px; }
        .sidebar nav { padding: 20px 0; flex: 1; }
        .sidebar a { display: flex; align-items: center; gap: 12px; padding: 14px 25px; color: rgba(255,255,255,0.75); text-decoration: none; font-size: 15px; font-weight: 500; transition: all 0.2s; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); color: white; }
        .sidebar a.active { background: rgba(255,255,255,0.15); color: white; border-left: 4px solid #52B788; }
        .sidebar a .icon { font-size: 18px; width: 24px; text-align: center; }

        /* MAIN */
        .main { margin-left: 260px; padding: 35px 40px; width: calc(100% - 260px); min-height: 100vh; }
        .page-title { font-size: 26px; font-weight: 800; color: #1B4332; }
        .page-subtitle { font-size: 13px; color: #95a5a6; margin-bottom: 8px; }

        /* BUTTONS */
        .btn { display: inline-block; padding: 10px 22px; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; border: none; font-size: 14px; }
        .btn-green { background: #1B4332; color: white; }
        .btn-green:hover { background: #2D6A4F; }
        .btn-blue { background: #1976D2; color: white; }
        .btn-gray { background: #f0f0f0; color: #333; }
        .btn-red { background: #e74c3c; color: white; }

        /* CARDS */
        .card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); }

        /* TABLE */
        table { width: 100%; border-collapse: collapse; }
        table th { background: #f8f9fa; padding: 13px 15px; text-align: left; font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        table td { padding: 13px 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        table tr:hover { background: #fafafa; }

        /* BADGES */
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-green { background: #D8F3DC; color: #1B4332; }
        .badge-blue { background: #E3F2FD; color: #1976D2; }
        .badge-orange { background: #FFF3E0; color: #E65100; }
        .badge-red { background: #FFEBEE; color: #C62828; }

        /* FORMS */
        form label { display: block; font-weight: 600; color: #555; margin-bottom: 5px; margin-top: 15px; font-size: 14px; }
        form input, form select, form textarea { width: 100%; padding: 11px 14px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: border 0.2s; }
        form input:focus, form select:focus, form textarea:focus { border-color: #1B4332; outline: none; }
        form textarea { height: 120px; resize: vertical; }

        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    </style>
    {% block stylesheets %}{% endblock %}
</head>
<body>
    <div class=\"sidebar\">
        <div class=\"logo-area\">
            <div class=\"logo-icon\">💧</div>
            <div class=\"logo-text\">AGRIFLOW</div>
        </div>
        <nav>
            {% block sidebar %}{% endblock %}
        </nav>
    </div>
    <div class=\"main\">
        {% block body %}{% endblock %}
    </div>
</body>
</html>", "base.html.twig", "C:\\Users\\wess\\irrigation3\\irrigation6\\templates\\base.html.twig");
    }
}

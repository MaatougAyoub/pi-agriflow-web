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

/* expert/irrigation_detail.html.twig */
class __TwigTemplate_a56ce87a2a0d1787bcd855d5008bbd7d extends Template
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

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'sidebar' => [$this, 'block_sidebar'],
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "expert/irrigation_detail.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "expert/irrigation_detail.html.twig"));

        $this->parent = $this->load("base.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 2
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

        yield "Détail Plan - Expert";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 4
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

        // line 5
        yield "    <a href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_home");
        yield "\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"";
        // line 6
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_irrigation");
        yield "\" class=\"active\"><span class=\"icon\">💧</span> Irrigation</a>
    <a href=\"";
        // line 7
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_diagnostics");
        yield "\"><span class=\"icon\">📝</span> Diagnostics</a>
    <a href=\"";
        // line 8
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_produits");
        yield "\"><span class=\"icon\">🧪</span> Produits</a>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 11
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

        // line 12
        yield "<div style=\"background:linear-gradient(135deg,#1B4332,#2D6A4F);border-radius:14px;
            padding:30px 35px;color:white;display:flex;justify-content:space-between;
            align-items:center;margin-bottom:30px\">
    <div>
        <div style=\"font-size:13px;opacity:0.8;margin-bottom:6px;text-transform:uppercase;letter-spacing:1px\">
            Expert — Remplir le plan
        </div>
        <div style=\"font-size:24px;font-weight:800;margin-bottom:8px\">";
        // line 19
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["plan"]) || array_key_exists("plan", $context) ? $context["plan"] : (function () { throw new RuntimeError('Variable "plan" does not exist.', 19, $this->source); })()), "culture", [], "any", false, false, false, 19), "nom", [], "any", false, false, false, 19), "html", null, true);
        yield "</div>
        <div style=\"opacity:0.85;font-size:14px\">
            Parcelle: ";
        // line 21
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["plan"]) || array_key_exists("plan", $context) ? $context["plan"] : (function () { throw new RuntimeError('Variable "plan" does not exist.', 21, $this->source); })()), "culture", [], "any", false, false, false, 21), "superficie", [], "any", false, false, false, 21), "html", null, true);
        yield " ha &nbsp;|&nbsp;
            Besoin: ";
        // line 22
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber(CoreExtension::getAttribute($this->env, $this->source, (isset($context["plan"]) || array_key_exists("plan", $context) ? $context["plan"] : (function () { throw new RuntimeError('Variable "plan" does not exist.', 22, $this->source); })()), "besoinEau", [], "any", false, false, false, 22), 1), "html", null, true);
        yield " mm &nbsp;|&nbsp;
            <span style=\"background:rgba(255,255,255,0.2);padding:3px 10px;border-radius:20px\">
                ";
        // line 24
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["plan"]) || array_key_exists("plan", $context) ? $context["plan"] : (function () { throw new RuntimeError('Variable "plan" does not exist.', 24, $this->source); })()), "statut", [], "any", false, false, false, 24), "html", null, true);
        yield "
            </span>
        </div>
    </div>
    <div style=\"text-align:center;opacity:0.9\">
        <div style=\"font-size:42px\">💧</div>
        <div style=\"font-size:11px;letter-spacing:2px;margin-top:4px\">AGRIFLOW</div>
    </div>
</div>

<div class=\"card\">
    <h3 style=\"margin-bottom:25px;color:#1B4332;font-size:17px\">📅 Planning Hebdomadaire</h3>

    <form method=\"post\" action=\"";
        // line 37
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_irrigation_save", ["id" => CoreExtension::getAttribute($this->env, $this->source, (isset($context["plan"]) || array_key_exists("plan", $context) ? $context["plan"] : (function () { throw new RuntimeError('Variable "plan" does not exist.', 37, $this->source); })()), "id", [], "any", false, false, false, 37)]), "html", null, true);
        yield "\">
        <div style=\"overflow-x:auto\">
        <table style=\"border-collapse:separate;border-spacing:0\">
            ";
        // line 40
        $context["jours"] = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
        // line 41
        yield "            ";
        $context["joursKeys"] = ["LUN", "MAR", "MER", "JEU", "VEN", "SAM", "DIM"];
        // line 42
        yield "            <thead>
                <tr>
                    <th style=\"width:130px;background:white;border-bottom:2px solid #f0f0f0\"></th>
                    ";
        // line 45
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["jours"]) || array_key_exists("jours", $context) ? $context["jours"] : (function () { throw new RuntimeError('Variable "jours" does not exist.', 45, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["j"]) {
            // line 46
            yield "                    <th style=\"text-align:center;padding:12px 8px;background:white;
                               border-bottom:2px solid #f0f0f0;color:#1B4332;font-weight:700\">
                        ";
            // line 48
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["j"], "html", null, true);
            yield "
                    </th>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['j'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 51
        yield "                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style=\"font-weight:700;font-size:12px;color:#666;text-transform:uppercase;padding:12px 8px;border-bottom:1px solid #f5f5f5\">EAU (mm)</td>
                    ";
        // line 56
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["joursKeys"]) || array_key_exists("joursKeys", $context) ? $context["joursKeys"] : (function () { throw new RuntimeError('Variable "joursKeys" does not exist.', 56, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["k"]) {
            // line 57
            yield "                    <td style=\"padding:8px;border-bottom:1px solid #f5f5f5;text-align:center\">
                        <input type=\"number\" step=\"0.1\" name=\"eau_";
            // line 58
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["k"], "html", null, true);
            yield "\"
                               value=\"";
            // line 59
            yield (((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["jourData"] ?? null), $context["k"], [], "array", false, true, false, 59), "eauMm", [], "any", true, true, false, 59) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["jourData"]) || array_key_exists("jourData", $context) ? $context["jourData"] : (function () { throw new RuntimeError('Variable "jourData" does not exist.', 59, $this->source); })()), $context["k"], [], "array", false, false, false, 59), "eauMm", [], "any", false, false, false, 59)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["jourData"]) || array_key_exists("jourData", $context) ? $context["jourData"] : (function () { throw new RuntimeError('Variable "jourData" does not exist.', 59, $this->source); })()), $context["k"], [], "array", false, false, false, 59), "eauMm", [], "any", false, false, false, 59), "html", null, true)) : (0.0));
            yield "\"
                               style=\"width:80px;padding:8px;text-align:center;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-weight:600\">
                    </td>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['k'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 63
        yield "                </tr>
                <tr>
                    <td style=\"font-weight:700;font-size:12px;color:#666;text-transform:uppercase;padding:12px 8px;border-bottom:1px solid #f5f5f5\">DURÉE (min)</td>
                    ";
        // line 66
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["joursKeys"]) || array_key_exists("joursKeys", $context) ? $context["joursKeys"] : (function () { throw new RuntimeError('Variable "joursKeys" does not exist.', 66, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["k"]) {
            // line 67
            yield "                    <td style=\"padding:8px;border-bottom:1px solid #f5f5f5;text-align:center\">
                        <input type=\"number\" name=\"duree_";
            // line 68
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["k"], "html", null, true);
            yield "\"
                               value=\"";
            // line 69
            yield (((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["jourData"] ?? null), $context["k"], [], "array", false, true, false, 69), "dureeMin", [], "any", true, true, false, 69) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["jourData"]) || array_key_exists("jourData", $context) ? $context["jourData"] : (function () { throw new RuntimeError('Variable "jourData" does not exist.', 69, $this->source); })()), $context["k"], [], "array", false, false, false, 69), "dureeMin", [], "any", false, false, false, 69)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["jourData"]) || array_key_exists("jourData", $context) ? $context["jourData"] : (function () { throw new RuntimeError('Variable "jourData" does not exist.', 69, $this->source); })()), $context["k"], [], "array", false, false, false, 69), "dureeMin", [], "any", false, false, false, 69), "html", null, true)) : (0));
            yield "\"
                               style=\"width:80px;padding:8px;text-align:center;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-weight:600\">
                    </td>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['k'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 73
        yield "                </tr>
                <tr>
                    <td style=\"font-weight:700;font-size:12px;color:#666;text-transform:uppercase;padding:12px 8px;border-bottom:1px solid #f5f5f5\">TEMP (°C)</td>
                    ";
        // line 76
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["joursKeys"]) || array_key_exists("joursKeys", $context) ? $context["joursKeys"] : (function () { throw new RuntimeError('Variable "joursKeys" does not exist.', 76, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["k"]) {
            // line 77
            yield "                    <td style=\"padding:8px;border-bottom:1px solid #f5f5f5;text-align:center\">
                        <input type=\"number\" step=\"0.1\" name=\"temp_";
            // line 78
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["k"], "html", null, true);
            yield "\"
                               value=\"";
            // line 79
            yield (((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["jourData"] ?? null), $context["k"], [], "array", false, true, false, 79), "temperature", [], "any", true, true, false, 79) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["jourData"]) || array_key_exists("jourData", $context) ? $context["jourData"] : (function () { throw new RuntimeError('Variable "jourData" does not exist.', 79, $this->source); })()), $context["k"], [], "array", false, false, false, 79), "temperature", [], "any", false, false, false, 79)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["jourData"]) || array_key_exists("jourData", $context) ? $context["jourData"] : (function () { throw new RuntimeError('Variable "jourData" does not exist.', 79, $this->source); })()), $context["k"], [], "array", false, false, false, 79), "temperature", [], "any", false, false, false, 79), "html", null, true)) : (0.0));
            yield "\"
                               style=\"width:80px;padding:8px;text-align:center;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-weight:600\">
                    </td>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['k'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 83
        yield "                </tr>
                <tr>
                    <td style=\"font-weight:700;font-size:12px;color:#666;text-transform:uppercase;padding:12px 8px;border-bottom:1px solid #f5f5f5\">HUMIDITÉ (%)</td>
                    ";
        // line 86
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["joursKeys"]) || array_key_exists("joursKeys", $context) ? $context["joursKeys"] : (function () { throw new RuntimeError('Variable "joursKeys" does not exist.', 86, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["k"]) {
            // line 87
            yield "                    <td style=\"padding:8px;border-bottom:1px solid #f5f5f5;text-align:center\">
                        <input type=\"number\" step=\"0.1\" name=\"hum_";
            // line 88
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["k"], "html", null, true);
            yield "\"
                               value=\"";
            // line 89
            yield (((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["jourData"] ?? null), $context["k"], [], "array", false, true, false, 89), "humidite", [], "any", true, true, false, 89) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["jourData"]) || array_key_exists("jourData", $context) ? $context["jourData"] : (function () { throw new RuntimeError('Variable "jourData" does not exist.', 89, $this->source); })()), $context["k"], [], "array", false, false, false, 89), "humidite", [], "any", false, false, false, 89)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["jourData"]) || array_key_exists("jourData", $context) ? $context["jourData"] : (function () { throw new RuntimeError('Variable "jourData" does not exist.', 89, $this->source); })()), $context["k"], [], "array", false, false, false, 89), "humidite", [], "any", false, false, false, 89), "html", null, true)) : (0.0));
            yield "\"
                               style=\"width:80px;padding:8px;text-align:center;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-weight:600\">
                    </td>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['k'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 93
        yield "                </tr>
                <tr>
                    <td style=\"font-weight:700;font-size:12px;color:#666;text-transform:uppercase;padding:12px 8px\">PLUIE (mm)</td>
                    ";
        // line 96
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["joursKeys"]) || array_key_exists("joursKeys", $context) ? $context["joursKeys"] : (function () { throw new RuntimeError('Variable "joursKeys" does not exist.', 96, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["k"]) {
            // line 97
            yield "                    <td style=\"padding:8px;text-align:center\">
                        <input type=\"number\" step=\"0.1\" name=\"pluie_";
            // line 98
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["k"], "html", null, true);
            yield "\"
                               value=\"";
            // line 99
            yield (((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, ($context["jourData"] ?? null), $context["k"], [], "array", false, true, false, 99), "pluieMm", [], "any", true, true, false, 99) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["jourData"]) || array_key_exists("jourData", $context) ? $context["jourData"] : (function () { throw new RuntimeError('Variable "jourData" does not exist.', 99, $this->source); })()), $context["k"], [], "array", false, false, false, 99), "pluieMm", [], "any", false, false, false, 99)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["jourData"]) || array_key_exists("jourData", $context) ? $context["jourData"] : (function () { throw new RuntimeError('Variable "jourData" does not exist.', 99, $this->source); })()), $context["k"], [], "array", false, false, false, 99), "pluieMm", [], "any", false, false, false, 99), "html", null, true)) : (0.0));
            yield "\"
                               style=\"width:80px;padding:8px;text-align:center;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-weight:600\">
                    </td>
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['k'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 103
        yield "                </tr>
            </tbody>
        </table>
        </div>

        <div style=\"display:flex;justify-content:space-between;align-items:center;margin-top:25px\">
            <a href=\"";
        // line 109
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_irrigation");
        yield "\" class=\"btn btn-gray\">← Retour</a>
            <button type=\"submit\" class=\"btn btn-green\">💾 Enregistrer</button>
        </div>
    </form>
</div>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "expert/irrigation_detail.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  352 => 109,  344 => 103,  334 => 99,  330 => 98,  327 => 97,  323 => 96,  318 => 93,  308 => 89,  304 => 88,  301 => 87,  297 => 86,  292 => 83,  282 => 79,  278 => 78,  275 => 77,  271 => 76,  266 => 73,  256 => 69,  252 => 68,  249 => 67,  245 => 66,  240 => 63,  230 => 59,  226 => 58,  223 => 57,  219 => 56,  212 => 51,  203 => 48,  199 => 46,  195 => 45,  190 => 42,  187 => 41,  185 => 40,  179 => 37,  163 => 24,  158 => 22,  154 => 21,  149 => 19,  140 => 12,  127 => 11,  114 => 8,  110 => 7,  106 => 6,  101 => 5,  88 => 4,  65 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}
{% block title %}Détail Plan - Expert{% endblock %}

{% block sidebar %}
    <a href=\"{{ path('expert_home') }}\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"{{ path('expert_irrigation') }}\" class=\"active\"><span class=\"icon\">💧</span> Irrigation</a>
    <a href=\"{{ path('expert_diagnostics') }}\"><span class=\"icon\">📝</span> Diagnostics</a>
    <a href=\"{{ path('expert_produits') }}\"><span class=\"icon\">🧪</span> Produits</a>
{% endblock %}

{% block body %}
<div style=\"background:linear-gradient(135deg,#1B4332,#2D6A4F);border-radius:14px;
            padding:30px 35px;color:white;display:flex;justify-content:space-between;
            align-items:center;margin-bottom:30px\">
    <div>
        <div style=\"font-size:13px;opacity:0.8;margin-bottom:6px;text-transform:uppercase;letter-spacing:1px\">
            Expert — Remplir le plan
        </div>
        <div style=\"font-size:24px;font-weight:800;margin-bottom:8px\">{{ plan.culture.nom }}</div>
        <div style=\"opacity:0.85;font-size:14px\">
            Parcelle: {{ plan.culture.superficie }} ha &nbsp;|&nbsp;
            Besoin: {{ plan.besoinEau|number_format(1) }} mm &nbsp;|&nbsp;
            <span style=\"background:rgba(255,255,255,0.2);padding:3px 10px;border-radius:20px\">
                {{ plan.statut }}
            </span>
        </div>
    </div>
    <div style=\"text-align:center;opacity:0.9\">
        <div style=\"font-size:42px\">💧</div>
        <div style=\"font-size:11px;letter-spacing:2px;margin-top:4px\">AGRIFLOW</div>
    </div>
</div>

<div class=\"card\">
    <h3 style=\"margin-bottom:25px;color:#1B4332;font-size:17px\">📅 Planning Hebdomadaire</h3>

    <form method=\"post\" action=\"{{ path('expert_irrigation_save', {id: plan.id}) }}\">
        <div style=\"overflow-x:auto\">
        <table style=\"border-collapse:separate;border-spacing:0\">
            {% set jours = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] %}
            {% set joursKeys = ['LUN','MAR','MER','JEU','VEN','SAM','DIM'] %}
            <thead>
                <tr>
                    <th style=\"width:130px;background:white;border-bottom:2px solid #f0f0f0\"></th>
                    {% for j in jours %}
                    <th style=\"text-align:center;padding:12px 8px;background:white;
                               border-bottom:2px solid #f0f0f0;color:#1B4332;font-weight:700\">
                        {{ j }}
                    </th>
                    {% endfor %}
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style=\"font-weight:700;font-size:12px;color:#666;text-transform:uppercase;padding:12px 8px;border-bottom:1px solid #f5f5f5\">EAU (mm)</td>
                    {% for k in joursKeys %}
                    <td style=\"padding:8px;border-bottom:1px solid #f5f5f5;text-align:center\">
                        <input type=\"number\" step=\"0.1\" name=\"eau_{{ k }}\"
                               value=\"{{ jourData[k].eauMm ?? 0.0 }}\"
                               style=\"width:80px;padding:8px;text-align:center;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-weight:600\">
                    </td>
                    {% endfor %}
                </tr>
                <tr>
                    <td style=\"font-weight:700;font-size:12px;color:#666;text-transform:uppercase;padding:12px 8px;border-bottom:1px solid #f5f5f5\">DURÉE (min)</td>
                    {% for k in joursKeys %}
                    <td style=\"padding:8px;border-bottom:1px solid #f5f5f5;text-align:center\">
                        <input type=\"number\" name=\"duree_{{ k }}\"
                               value=\"{{ jourData[k].dureeMin ?? 0 }}\"
                               style=\"width:80px;padding:8px;text-align:center;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-weight:600\">
                    </td>
                    {% endfor %}
                </tr>
                <tr>
                    <td style=\"font-weight:700;font-size:12px;color:#666;text-transform:uppercase;padding:12px 8px;border-bottom:1px solid #f5f5f5\">TEMP (°C)</td>
                    {% for k in joursKeys %}
                    <td style=\"padding:8px;border-bottom:1px solid #f5f5f5;text-align:center\">
                        <input type=\"number\" step=\"0.1\" name=\"temp_{{ k }}\"
                               value=\"{{ jourData[k].temperature ?? 0.0 }}\"
                               style=\"width:80px;padding:8px;text-align:center;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-weight:600\">
                    </td>
                    {% endfor %}
                </tr>
                <tr>
                    <td style=\"font-weight:700;font-size:12px;color:#666;text-transform:uppercase;padding:12px 8px;border-bottom:1px solid #f5f5f5\">HUMIDITÉ (%)</td>
                    {% for k in joursKeys %}
                    <td style=\"padding:8px;border-bottom:1px solid #f5f5f5;text-align:center\">
                        <input type=\"number\" step=\"0.1\" name=\"hum_{{ k }}\"
                               value=\"{{ jourData[k].humidite ?? 0.0 }}\"
                               style=\"width:80px;padding:8px;text-align:center;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-weight:600\">
                    </td>
                    {% endfor %}
                </tr>
                <tr>
                    <td style=\"font-weight:700;font-size:12px;color:#666;text-transform:uppercase;padding:12px 8px\">PLUIE (mm)</td>
                    {% for k in joursKeys %}
                    <td style=\"padding:8px;text-align:center\">
                        <input type=\"number\" step=\"0.1\" name=\"pluie_{{ k }}\"
                               value=\"{{ jourData[k].pluieMm ?? 0.0 }}\"
                               style=\"width:80px;padding:8px;text-align:center;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-weight:600\">
                    </td>
                    {% endfor %}
                </tr>
            </tbody>
        </table>
        </div>

        <div style=\"display:flex;justify-content:space-between;align-items:center;margin-top:25px\">
            <a href=\"{{ path('expert_irrigation') }}\" class=\"btn btn-gray\">← Retour</a>
            <button type=\"submit\" class=\"btn btn-green\">💾 Enregistrer</button>
        </div>
    </form>
</div>
{% endblock %}", "expert/irrigation_detail.html.twig", "C:\\Users\\wess\\irrigation3\\irrigation6\\templates\\expert\\irrigation_detail.html.twig");
    }
}

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

/* expert/home.html.twig */
class __TwigTemplate_36417919d468404f829bc539caea207e extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "expert/home.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "expert/home.html.twig"));

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

        yield "Expert - AGRIFLOW";
        
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
        yield "\" class=\"active\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"";
        // line 6
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_irrigation");
        yield "\"><span class=\"icon\">💧</span> Irrigation</a>
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
        yield "<div style=\"margin-bottom:30px\">
    <div class=\"page-subtitle\">Bienvenue sur AGRIFLOW</div>
    <div class=\"page-title\">Tableau de bord Expert</div>
</div>

<div style=\"display:flex;gap:20px;margin-bottom:30px\">
    <div class=\"card\" style=\"flex:1;text-align:center;padding:30px\">
        <div style=\"font-size:42px;font-weight:900;color:#FF9800\">
            ";
        // line 20
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::length($this->env->getCharset(), Twig\Extension\CoreExtension::filter($this->env, (isset($context["diagnostics"]) || array_key_exists("diagnostics", $context) ? $context["diagnostics"] : (function () { throw new RuntimeError('Variable "diagnostics" does not exist.', 20, $this->source); })()), function ($__d__) use ($context, $macros) { $context["d"] = $__d__; return (CoreExtension::getAttribute($this->env, $this->source, (isset($context["d"]) || array_key_exists("d", $context) ? $context["d"] : (function () { throw new RuntimeError('Variable "d" does not exist.', 20, $this->source); })()), "statut", [], "any", false, false, false, 20) == "en_attente"); })), "html", null, true);
        yield "
        </div>
        <div style=\"color:#95a5a6;margin-top:8px;font-size:14px\">Diagnostics en attente</div>
    </div>
    <div class=\"card\" style=\"flex:1;text-align:center;padding:30px\">
        <div style=\"font-size:42px;font-weight:900;color:#4CAF50\">
            ";
        // line 26
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::length($this->env->getCharset(), Twig\Extension\CoreExtension::filter($this->env, (isset($context["diagnostics"]) || array_key_exists("diagnostics", $context) ? $context["diagnostics"] : (function () { throw new RuntimeError('Variable "diagnostics" does not exist.', 26, $this->source); })()), function ($__d__) use ($context, $macros) { $context["d"] = $__d__; return (CoreExtension::getAttribute($this->env, $this->source, (isset($context["d"]) || array_key_exists("d", $context) ? $context["d"] : (function () { throw new RuntimeError('Variable "d" does not exist.', 26, $this->source); })()), "statut", [], "any", false, false, false, 26) == "traite"); })), "html", null, true);
        yield "
        </div>
        <div style=\"color:#95a5a6;margin-top:8px;font-size:14px\">Diagnostics traités</div>
    </div>
    <div class=\"card\" style=\"flex:1;text-align:center;padding:30px\">
        <div style=\"font-size:42px;font-weight:900;color:#1B4332\">";
        // line 31
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["total_produits"]) || array_key_exists("total_produits", $context) ? $context["total_produits"] : (function () { throw new RuntimeError('Variable "total_produits" does not exist.', 31, $this->source); })()), "html", null, true);
        yield "</div>
        <div style=\"color:#95a5a6;margin-top:8px;font-size:14px\">Produits disponibles</div>
    </div>
</div>

<div class=\"card\">
    <h3 style=\"color:#1B4332;margin-bottom:20px;font-size:17px\">📝 Derniers diagnostics</h3>
    <table>
        <tr>
            <th>Culture</th><th>Description</th><th>Statut</th><th>Date</th>
        </tr>
        ";
        // line 42
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(Twig\Extension\CoreExtension::slice($this->env->getCharset(), (isset($context["diagnostics"]) || array_key_exists("diagnostics", $context) ? $context["diagnostics"] : (function () { throw new RuntimeError('Variable "diagnostics" does not exist.', 42, $this->source); })()), 0, 5));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["d"]) {
            // line 43
            yield "        <tr>
            <td><strong>";
            // line 44
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["d"], "nomCulture", [], "any", false, false, false, 44), "html", null, true);
            yield "</strong></td>
            <td style=\"color:#6B7280\">";
            // line 45
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::slice($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["d"], "description", [], "any", false, false, false, 45), 0, 50), "html", null, true);
            yield "...</td>
            <td>
                ";
            // line 47
            if ((CoreExtension::getAttribute($this->env, $this->source, $context["d"], "statut", [], "any", false, false, false, 47) == "traite")) {
                // line 48
                yield "                    <span class=\"badge\" style=\"background:#E8F5E9;color:#4CAF50\">TRAITÉ</span>
                ";
            } else {
                // line 50
                yield "                    <span class=\"badge\" style=\"background:#FFF4E5;color:#FF9800\">EN ATTENTE</span>
                ";
            }
            // line 52
            yield "            </td>
            <td style=\"color:#95a5a6\">";
            // line 53
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["d"], "dateEnvoi", [], "any", false, false, false, 53), "d/m/Y"), "html", null, true);
            yield "</td>
        </tr>
        ";
            $context['_iterated'] = true;
        }
        // line 55
        if (!$context['_iterated']) {
            // line 56
            yield "        <tr><td colspan=\"4\" style=\"text-align:center;color:#95a5a6;padding:30px\">Aucun diagnostic.</td></tr>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['d'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 58
        yield "    </table>
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
        return "expert/home.html.twig";
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
        return array (  227 => 58,  220 => 56,  218 => 55,  211 => 53,  208 => 52,  204 => 50,  200 => 48,  198 => 47,  193 => 45,  189 => 44,  186 => 43,  181 => 42,  167 => 31,  159 => 26,  150 => 20,  140 => 12,  127 => 11,  114 => 8,  110 => 7,  106 => 6,  101 => 5,  88 => 4,  65 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}
{% block title %}Expert - AGRIFLOW{% endblock %}

{% block sidebar %}
    <a href=\"{{ path('expert_home') }}\" class=\"active\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"{{ path('expert_irrigation') }}\"><span class=\"icon\">💧</span> Irrigation</a>
    <a href=\"{{ path('expert_diagnostics') }}\"><span class=\"icon\">📝</span> Diagnostics</a>
    <a href=\"{{ path('expert_produits') }}\"><span class=\"icon\">🧪</span> Produits</a>
{% endblock %}

{% block body %}
<div style=\"margin-bottom:30px\">
    <div class=\"page-subtitle\">Bienvenue sur AGRIFLOW</div>
    <div class=\"page-title\">Tableau de bord Expert</div>
</div>

<div style=\"display:flex;gap:20px;margin-bottom:30px\">
    <div class=\"card\" style=\"flex:1;text-align:center;padding:30px\">
        <div style=\"font-size:42px;font-weight:900;color:#FF9800\">
            {{ diagnostics|filter(d => d.statut == 'en_attente')|length }}
        </div>
        <div style=\"color:#95a5a6;margin-top:8px;font-size:14px\">Diagnostics en attente</div>
    </div>
    <div class=\"card\" style=\"flex:1;text-align:center;padding:30px\">
        <div style=\"font-size:42px;font-weight:900;color:#4CAF50\">
            {{ diagnostics|filter(d => d.statut == 'traite')|length }}
        </div>
        <div style=\"color:#95a5a6;margin-top:8px;font-size:14px\">Diagnostics traités</div>
    </div>
    <div class=\"card\" style=\"flex:1;text-align:center;padding:30px\">
        <div style=\"font-size:42px;font-weight:900;color:#1B4332\">{{ total_produits }}</div>
        <div style=\"color:#95a5a6;margin-top:8px;font-size:14px\">Produits disponibles</div>
    </div>
</div>

<div class=\"card\">
    <h3 style=\"color:#1B4332;margin-bottom:20px;font-size:17px\">📝 Derniers diagnostics</h3>
    <table>
        <tr>
            <th>Culture</th><th>Description</th><th>Statut</th><th>Date</th>
        </tr>
        {% for d in diagnostics|slice(0,5) %}
        <tr>
            <td><strong>{{ d.nomCulture }}</strong></td>
            <td style=\"color:#6B7280\">{{ d.description|slice(0,50) }}...</td>
            <td>
                {% if d.statut == 'traite' %}
                    <span class=\"badge\" style=\"background:#E8F5E9;color:#4CAF50\">TRAITÉ</span>
                {% else %}
                    <span class=\"badge\" style=\"background:#FFF4E5;color:#FF9800\">EN ATTENTE</span>
                {% endif %}
            </td>
            <td style=\"color:#95a5a6\">{{ d.dateEnvoi|date('d/m/Y') }}</td>
        </tr>
        {% else %}
        <tr><td colspan=\"4\" style=\"text-align:center;color:#95a5a6;padding:30px\">Aucun diagnostic.</td></tr>
        {% endfor %}
    </table>
</div>
{% endblock %}", "expert/home.html.twig", "C:\\Users\\wess\\irrigation3\\irrigation6\\templates\\expert\\home.html.twig");
    }
}

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

/* agriculteur/home.html.twig */
class __TwigTemplate_49cd3eca0336b43b76a49c4c4ddba3e5 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "agriculteur/home.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "agriculteur/home.html.twig"));

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

        yield "Dashboard - AGRIFLOW";
        
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
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("agriculteur_home");
        yield "\" class=\"active\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"";
        // line 6
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("agriculteur_irrigation");
        yield "\"><span class=\"icon\">💧</span> Plan d'Irrigation</a>
     <a href=\"";
        // line 7
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("agriculteur_diagnostics");
        yield "\"><span class=\"icon\">📝</span> Diagnostic</a>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 10
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

        // line 11
        yield "<div style=\"margin-bottom:30px\">
    <div class=\"page-subtitle\">Bienvenue sur AGRIFLOW</div>
    <div class=\"page-title\">Tableau de bord</div>
</div>

<div style=\"display:flex;gap:20px;margin-bottom:30px\">
    <div class=\"card\" style=\"flex:1;text-align:center;padding:30px\">
        <div style=\"font-size:42px;font-weight:900;color:#1B4332\">";
        // line 18
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::length($this->env->getCharset(), (isset($context["plans"]) || array_key_exists("plans", $context) ? $context["plans"] : (function () { throw new RuntimeError('Variable "plans" does not exist.', 18, $this->source); })())), "html", null, true);
        yield "</div>
        <div style=\"color:#95a5a6;margin-top:8px;font-size:14px\">Plans d'irrigation</div>
    </div>
</div>

<div class=\"card\">
    <div style=\"display:flex;justify-content:space-between;align-items:center;margin-bottom:20px\">
        <h3 style=\"color:#1B4332;font-size:17px\">💧 Mes derniers plans</h3>
        ";
        // line 27
        yield "        <div style=\"position:relative\">
            <input type=\"text\" id=\"searchInput\" placeholder=\"🔍 Rechercher une culture...\"
                   onkeyup=\"filtrerPlans()\"
                   style=\"padding:10px 16px;border:1.5px solid #e0e0e0;border-radius:8px;
                          font-size:14px;width:260px;outline:none;
                          transition:border 0.2s;\"
                   onfocus=\"this.style.borderColor='#1B4332'\"
                   onblur=\"this.style.borderColor='#e0e0e0'\">
        </div>
    </div>

    <table id=\"plansTable\">
        <thead>
            <tr>
                <th>Culture</th>
                <th>Besoin eau</th>
                <th>Statut</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            ";
        // line 48
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["plans"]) || array_key_exists("plans", $context) ? $context["plans"] : (function () { throw new RuntimeError('Variable "plans" does not exist.', 48, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["p"]) {
            // line 49
            yield "            <tr class=\"plan-row\">
                <td><strong>";
            // line 50
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["p"], "culture", [], "any", false, false, false, 50), "nom", [], "any", false, false, false, 50), "html", null, true);
            yield "</strong></td>
                <td style=\"color:#1976D2;font-weight:600\">";
            // line 51
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber(CoreExtension::getAttribute($this->env, $this->source, $context["p"], "besoinEau", [], "any", false, false, false, 51), 1), "html", null, true);
            yield " mm</td>
                <td><span class=\"badge badge-green\">";
            // line 52
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["p"], "statut", [], "any", false, false, false, 52), "html", null, true);
            yield "</span></td>
                <td style=\"color:#95a5a6\">";
            // line 53
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["p"], "dateCreation", [], "any", false, false, false, 53), "d/m/Y"), "html", null, true);
            yield "</td>
            </tr>
            ";
            $context['_iterated'] = true;
        }
        // line 55
        if (!$context['_iterated']) {
            // line 56
            yield "            <tr><td colspan=\"4\" style=\"text-align:center;color:#95a5a6;padding:30px\">Aucun plan.</td></tr>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['p'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 58
        yield "        </tbody>
    </table>

    <div id=\"noResult\" style=\"display:none;text-align:center;color:#95a5a6;padding:30px\">
        Aucune culture trouvée.
    </div>
</div>

<script>
function filtrerPlans() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.plan-row');
    let count = 0;

    rows.forEach(row => {
        const nom = row.querySelector('td strong').textContent.toLowerCase();
        if (nom.includes(input)) {
            row.style.display = '';
            count++;
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('noResult').style.display = count === 0 ? 'block' : 'none';
}
</script>
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
        return "agriculteur/home.html.twig";
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
        return array (  215 => 58,  208 => 56,  206 => 55,  199 => 53,  195 => 52,  191 => 51,  187 => 50,  184 => 49,  179 => 48,  156 => 27,  145 => 18,  136 => 11,  123 => 10,  110 => 7,  106 => 6,  101 => 5,  88 => 4,  65 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}
{% block title %}Dashboard - AGRIFLOW{% endblock %}

{% block sidebar %}
    <a href=\"{{ path('agriculteur_home') }}\" class=\"active\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"{{ path('agriculteur_irrigation') }}\"><span class=\"icon\">💧</span> Plan d'Irrigation</a>
     <a href=\"{{ path('agriculteur_diagnostics') }}\"><span class=\"icon\">📝</span> Diagnostic</a>
{% endblock %}

{% block body %}
<div style=\"margin-bottom:30px\">
    <div class=\"page-subtitle\">Bienvenue sur AGRIFLOW</div>
    <div class=\"page-title\">Tableau de bord</div>
</div>

<div style=\"display:flex;gap:20px;margin-bottom:30px\">
    <div class=\"card\" style=\"flex:1;text-align:center;padding:30px\">
        <div style=\"font-size:42px;font-weight:900;color:#1B4332\">{{ plans|length }}</div>
        <div style=\"color:#95a5a6;margin-top:8px;font-size:14px\">Plans d'irrigation</div>
    </div>
</div>

<div class=\"card\">
    <div style=\"display:flex;justify-content:space-between;align-items:center;margin-bottom:20px\">
        <h3 style=\"color:#1B4332;font-size:17px\">💧 Mes derniers plans</h3>
        {# Barre de recherche #}
        <div style=\"position:relative\">
            <input type=\"text\" id=\"searchInput\" placeholder=\"🔍 Rechercher une culture...\"
                   onkeyup=\"filtrerPlans()\"
                   style=\"padding:10px 16px;border:1.5px solid #e0e0e0;border-radius:8px;
                          font-size:14px;width:260px;outline:none;
                          transition:border 0.2s;\"
                   onfocus=\"this.style.borderColor='#1B4332'\"
                   onblur=\"this.style.borderColor='#e0e0e0'\">
        </div>
    </div>

    <table id=\"plansTable\">
        <thead>
            <tr>
                <th>Culture</th>
                <th>Besoin eau</th>
                <th>Statut</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            {% for p in plans %}
            <tr class=\"plan-row\">
                <td><strong>{{ p.culture.nom }}</strong></td>
                <td style=\"color:#1976D2;font-weight:600\">{{ p.besoinEau|number_format(1) }} mm</td>
                <td><span class=\"badge badge-green\">{{ p.statut }}</span></td>
                <td style=\"color:#95a5a6\">{{ p.dateCreation|date('d/m/Y') }}</td>
            </tr>
            {% else %}
            <tr><td colspan=\"4\" style=\"text-align:center;color:#95a5a6;padding:30px\">Aucun plan.</td></tr>
            {% endfor %}
        </tbody>
    </table>

    <div id=\"noResult\" style=\"display:none;text-align:center;color:#95a5a6;padding:30px\">
        Aucune culture trouvée.
    </div>
</div>

<script>
function filtrerPlans() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.plan-row');
    let count = 0;

    rows.forEach(row => {
        const nom = row.querySelector('td strong').textContent.toLowerCase();
        if (nom.includes(input)) {
            row.style.display = '';
            count++;
        } else {
            row.style.display = 'none';
        }
    });

    document.getElementById('noResult').style.display = count === 0 ? 'block' : 'none';
}
</script>
{% endblock %}", "agriculteur/home.html.twig", "C:\\Users\\wess\\irrigation3\\irrigation6\\templates\\agriculteur\\home.html.twig");
    }
}
